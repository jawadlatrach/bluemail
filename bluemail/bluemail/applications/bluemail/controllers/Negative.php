<?php namespace ma\applications\bluemail\controllers
{
    if (!defined('IS_MFW')) die('<pre>It\'s forbidden to access these files directly , access should be only via index.php </pre>');
    /**
     * @framework       Miami Framework
     * @version         1.1
     * @author          Miami Team
     * @copyright       Copyright (c) 2017 - 2018.	
     * @license		
     * @link	
     */
    use ma\mfw\application\Controller as Controller;
    use ma\mfw\database\Database as Database;
    use ma\mfw\http\Request as Request;
    use ma\mfw\http\Response as Response;
    use ma\mfw\http\Session as Session;
    use ma\mfw\www\URL as URL;  
    use ma\mfw\types\Arrays as Arrays;
    use ma\mfw\mail\MailBoxManager as MailBoxManager;
    use ma\mfw\pmta\PmtaHeader as PmtaHeader;
    use ma\mfw\globals\Server as GloblServers;
    use ma\applications\bluemail\helpers\PagesHelper as PagesHelper;
    use ma\mfw\exceptions\types\PageException as PageException; 
    
    /**
     * @name            Tools.controller 
     * @description     The Tools controller
     * @package		ma\applications\bluemail\controllers
     * @category        Controller
     * @author		Miami Team			
     */
    class Negative extends Controller 
    {
        /**
         * @name init
         * @description initializing proccess before the action method executed
         * @once
         * @protected
         */
        public function init() 
        {
            # connect to the default database 
            Database::secureConnect();

            # check authentication
            $user = Session::get('bluemail_connected_user');  
            
            if(!isset($user))
            {
                Response::redirect(URL::getCurrentApplicationURL() . RDS . 'authentication' . RDS . 'login.html');
            }
            
            # check authorization access
            if(!in_array(Arrays::getElement($user,'application_role_id'),array(1)))
            {
                throw new PageException("403 Access Denied",403);
            }
        }

        /**
         * @name index
         * @description the index action
         * @before init
         * @after closeConnection
         */
        public function index() 
        {}

        /**
         * @name words
         * @description the words action
         * @after closeConnection
         */
        public function words() 
        {
            #connect to the default database 
            Database::secureConnect();

            # check authentication
            $user = Session::get('bluemail_connected_user');  
            
            if(!isset($user))
            {
                Response::redirect(URL::getCurrentApplicationURL() . RDS . 'authentication' . RDS . 'login.html');
            }
            
            # set the menu item to active 
            $this->getMasterView()->set('menu_production_negative_words',true);
        }
        
        /**
         * @name retreive
         * @description the retreive action
         * @after closeConnection
         */ 
        public function retreive() 
        {
            # connect to the default database 
            Database::secureConnect();

            # check authentication
            $user = Session::get('bluemail_connected_user');  
            
            if(!isset($user))
            {
                Response::redirect(URL::getCurrentApplicationURL() . RDS . 'authentication' . RDS . 'login.html');
            }
            
            # retrieve all form data
            $formData = Request::getAllDataFromPOST();
            $result = '';
            
            # execution case
            if(isset($formData) && count($formData))
            {
                
                $email = Arrays::getElement($formData,'email');
                $password = Arrays::getElement($formData,'password');
                $subject = Arrays::getElement($formData,'subject');
                $folder = Arrays::getElement($formData,'folder');
                $fromName = Arrays::getElement($formData,'from-name');
                $header = new PmtaHeader();
                $header->convertHeaderTextToParameters(Arrays::getElement($formData,'header'));

                if (isset($email) && isset($password) && filter_var($email,FILTER_VALIDATE_EMAIL))
                {
                    

                    $mailbox = new MailBoxManager();
                    // check the ISP if exist first... create a function export mail ISP ...
                    $isp = $mailbox->getEmailsIsp($email);
                    $mailbox->setIsp(trim($isp));

                    # inbox case
                    $mailbox->setFolder($folder);
                    $mailbox->connect($email, $password);
                    $mailbox->sortEmails(); 
                    $emailsIds = $mailbox->getEmailsIds();
                    $index = 1; 
                    $result .= "___________________________________________________________________________________________________" . PHP_EOL;
                    
                    foreach ($emailsIds as $id) 
                    {
                        $headerParser = new PmtaHeader();
                        $headerParser->convertHeaderTextToParameters($mailbox->getEmailHeader($id));
                        $email = $mailbox->getEmail($id);
                        $email['header'] = $headerParser->getParameters();
                        
                        # start filtering 
                        
                        $subjectFilter = (strlen(trim($subject)) > 0) ? trim($email['subject']) == trim($subject) : true;
                        $fromNameFilter = (strlen(trim($fromName)) > 0) ? trim($email['from-name']) == trim($fromName) : true;
                        $headerFilter = true;
                        
                        if(strlen(trim(Arrays::getElement($formData,'header'))) > 0)
                        {
                            foreach ($header->getParameters() as $key => $value)
                            {
                                $headerFilter = $headerFilter && trim($email['header'][$key]) == trim($value); 
                            }  
                        }

                        if($subjectFilter == true && $fromNameFilter == true && $headerFilter == true)
                        {
                            $result .= "Email $index : " . PHP_EOL . PHP_EOL; 
                            $result .= $email['body-html'];
                            $result .= "___________________________________________________________________________________________________" . PHP_EOL; 
                            $index++;
                        }  
                    }

                    # disconnect from inbox 
                    $mailbox->disconnect();
                }
                
                $result = $index == 1 ? 'No Emails Found !' : $result;
            }

            # set the menu item to active 
            $this->getMasterView()->set('menu_production_negative_retreive',true);
            
            # set the result
            $this->getPageView()->set('result',$result);
            
            # check for message 
            PagesHelper::checkForMessageToPage($this);
        }
        
        /**
         * @name getWords
         * @description gets the creative by id into a JSON format
         * @type ajax
         * @before init
         * @after closeConnection
         */
        public function getWords() 
        {
            # check if the request is not AJAX request then return to index 
            if(!empty(GloblServers::get('HTTP_X_REQUESTED_WITH')) && strtolower(GloblServers::get('HTTP_X_REQUESTED_WITH')))
            {
                # prevent layout to be displayed
                $this->setShowMasterView(false);
                $this->setShowPageView(false);

                $formData = Request::getAllDataFromPOST();
                
                if(count($formData) && key_exists('urls',$formData))
                {
                    $words = array();
                    
                    foreach (explode("\n",$formData['urls']) as $link) 
                    {
                        $link = (strpos($link,'http') == FALSE) ? 'http://' . $link : $link;
                       
                        if(filter_var($link,FILTER_VALIDATE_URL))
                        {
                            $result = Request::getURLHeaderLessContents($link);
                            
                            if($result != null && strlen($result) > 0)
                            {
                                $lines = explode("\n",$result);
                                $template = "";
                                $matches = array();

                                foreach ($lines as $row) 
                                {
                                    $template .= htmlspecialchars($row);
                                }
                                
                                $template = utf8_encode(html_entity_decode($template));
                                
                                
                                if (preg_match_all('`<\s*?[^>]+\s*?>`',$template, $matches)) 
                                {
                                    $strCodeHtmlTpl = str_replace($matches[0], "", $template);
                                    $pageWords = array_values(str_word_count($strCodeHtmlTpl, 2));
                                    
                                    foreach ($pageWords as $value) 
                                    {
                                        $value = trim(strtolower($value));
                                        $ok = preg_match_all('`[^a-zA-Z]`', $value,$matches);
                                        
                                        if ($ok == 0) 
                                        {
                                            if (strlen($value) > 1) 
                                            {
                                                if (!in_array($value,$words)) 
                                                {
                                                    array_push($words,$value);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    # print the result 
                    die(json_encode(array( "words" => implode(PHP_EOL, $words))));
                }
            }
            else
            {
                # redirect to show list 
                Response::redirect(URL::getCurrentApplicationURL() . RDS . 'mail.html');
            }
        }
        
        /**
         * @name closeConnection
         * @description close any open connections
         * @protected
         */
        public function closeConnection() 
        {
            # disconnect from all databases 
            Database::secureDisconnect();
        }  
    } 
}