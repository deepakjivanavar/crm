<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/
	
	class WebServiceErrorCode {
		
		public static $SESSLIFEOVER = "SESSION_EXPIRED";
		public static $REFERENCEINVALID = "REFERENCE_INVALID";
		public static $SESSIONIDLE = "SESSION_LEFT_IDLE";
		public static $SESSIONIDINVALID = "INVALID_SESSIONID";
		public static $INVALIDUSERPWD = "INVALID_USER_CREDENTIALS";
		public static $AUTHREQUIRED = "AUTHENTICATION_REQUIRED";
		public static $AUTHFAILURE = "AUTHENTICATION_FAILURE";
		public static $ACCESSDENIED = "ACCESS_DENIED";
		public static $DATABASEQUERYERROR = "DATABASE_QUERY_ERROR";
		public static $MANDFIELDSMISSING = "MANDATORY_FIELDS_MISSING";
		public static $INVALIDID = "INVALID_ID_ATTRIBUTE";
		public static $QUERYSYNTAX = "QUERY_SYNTAX_ERROR";
		public static $INVALIDTOKEN = "INVALID_AUTH_TOKEN";
		public static $ACCESSKEYUNDEFINED = "ACCESSKEY_UNDEFINED";
		public static $RECORDNOTFOUND = "RECORD_NOT_FOUND";
		public static $UNKNOWNOPERATION = "UNKNOWN_OPERATION";
		public static $INTERNALERROR = "INTERNAL_SERVER_ERROR";
		public static $OPERATIONNOTSUPPORTED = "OPERATION_NOT_SUPPORTED";
		public static $UNKOWNENTITY = "UNKOWN_ENTITY";
		public static $INVALID_POTENTIAL_FOR_CONVERT_LEAD = "INVALID_POTENTIAL_FOR_CONVERTLEAD";
		public static $LEAD_ALREADY_CONVERTED = "LEAD_ALREADY_CONVERTED";
		public static $LEAD_RELATED_UPDATE_FAILED = "LEAD_RELATEDLIST_UPDATE_FAILED";
		public static $FAILED_TO_CREATE_RELATION = "FAILED_TO_CREATE_RELATION";
		public static $FAILED_TO_MARK_CONVERTED = "FAILED_TO_MARK_LEAD_CONVERTED";
		public static $INVALIDOLDPASSWORD = "INVALID_OLD_PASSWORD";
		public static $NEWPASSWORDMISMATCH = "NEW_PASSWORD_MISMATCH";
		public static $CHANGEPASSWORDFAILURE = "CHANGE_PASSWORD_FAILURE";
        public static $POTENTIAL_ALREADY_CONVERTED = "POTENTIAL_ALREADY_CONVERTED";
        public static $FAILED_TO_MARK_POTENTIAL_CONVERTED = "FAILED_TO_MARK_POTENTIAL_CONVERTED";
        public static $POTENTIAL_RELATED_UPDATE_FAILED = "POTENTIAL_RELATEDLIST_UPDATE_FAILED";
        public static $FAILED_TO_CREATE = "FAILED_TO_CREATE";
		public static $INACTIVECURRENCY = "CURRENCY_INACTIVE";
	}
	
?>
