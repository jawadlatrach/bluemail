<?php namespace ma\applications\bluemail\models\admin
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
    use ma\mfw\application\Model as Model;
    /**
     * @name            SuppressionProccess.model 
     * @description     The SuppressionProccess model
     * @package		ma\applications\bluemail\models\admin
     * @category        Model
     * @author		Miami Team			
     */
    class SuppressionProccess extends Model
    {
        #table 
        
        /**
         * @schema
         * @readwrite
         */
        protected $_schema = 'admin';
        
        /**
         * @table
         * @readwrite
         */
        protected $_table = 'suppression_proccesses';

        # Columns 

        /**
	 * @column
	 * @readwrite
         * @autoincrement
         * @primary
	 * @type integer
         * @nullable false
	 * @length 
	 */
        protected $_id;

        /**
	 * @column
	 * @readwrite
	 * @type integer
         * @nullable false
	 * @length 
	 */
        protected $_user_id;
        
        /**
	 * @column
	 * @readwrite
	 * @type integer
         * @nullable false
	 * @length 
	 */
        protected $_sponsor_id;

        /**
	 * @column
	 * @readwrite
	 * @type integer
         * @nullable false
	 * @length 
	 */
        protected $_offer_id;
        
        /**
	 * @column
	 * @readwrite
	 * @type text
         * @nullable false
	 * @length 20
	 */
        protected $_status;
        
        /**
	 * @column
	 * @readwrite
	 * @type text
         * @nullable false
	 * @length
	 */
        protected $_progress;
        
        /**
	 * @column
	 * @readwrite
	 * @type integer
         * @nullable false
	 * @length
	 */
        protected $_emails_found;
        
        /**
	 * @column
	 * @readwrite
	 * @type timestamp
         * @nullable false
	 * @length
	 */
        protected $_start_time;
        
        /**
	 * @column
	 * @readwrite
	 * @type timestamp
         * @nullable true
	 * @length
	 */
        protected $_finish_time;
    }
}