<?php namespace ma\mfw\exceptions\types
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
    use ma\mfw\exceptions\BaseException as BaseException;
    /**
     * @name            ArgumentException.class 
     * @description     It's an exception class that deals with arguments errors
     * @package		ma\mfw\exceptions\types
     * @category        Exception Class
     * @author		Miami Team			
     */
    class ArgumentException extends BaseException
    {}    
}