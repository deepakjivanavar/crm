<?php
/*+***********************************************************************************
 * The contents of this file are subject to the nectarcrm CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: nectarcrm CRM Open Source
 * The Initial Developer of the Original Code is nectarcrm.
 * Portions created by nectarcrm are Copyright (C) nectarcrm.
 * All Rights Reserved.
 *************************************************************************************/

vimport('~~/include/Webservices/Query.php');

class Calendar_FetchAgendaEvents_Action extends nectarcrm_BasicAjax_Action {

	public function process(nectarcrm_Request $request) {
		$result = array();
		$start = $request->get('startDate');
		$noOfDays = $request->get('numOfDays');
		$dbStartDateOject = DateTimeField::convertToDBTimeZone($start);
		$dbStartDateTime = $dbStartDateOject->format('Y-m-d H:i:s');

		$dbEndDateTime = $this->addDays($dbStartDateTime, $noOfDays);

		$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

		$query = 'SELECT nectarcrm_activity.subject, nectarcrm_activity.eventstatus, nectarcrm_activity.priority ,nectarcrm_activity.visibility,
						nectarcrm_activity.date_start, nectarcrm_activity.time_start, nectarcrm_activity.due_date, nectarcrm_activity.time_end,
						nectarcrm_crmentity.smownerid, nectarcrm_activity.activityid, nectarcrm_activity.activitytype, nectarcrm_activity.recurringtype,
						nectarcrm_activity.location FROM nectarcrm_activity
						INNER JOIN nectarcrm_crmentity ON nectarcrm_activity.activityid = nectarcrm_crmentity.crmid
						LEFT JOIN nectarcrm_users ON nectarcrm_crmentity.smownerid = nectarcrm_users.id
						LEFT JOIN nectarcrm_groups ON nectarcrm_crmentity.smownerid = nectarcrm_groups.groupid
						WHERE nectarcrm_crmentity.deleted=0 AND nectarcrm_activity.activityid > 0 AND nectarcrm_activity.activitytype NOT IN ("Emails","Task") AND ';

		$hideCompleted = $currentUser->get('hidecompletedevents');
		if ($hideCompleted) {
			$query.= "nectarcrm_activity.eventstatus != 'HELD' AND ";
		}
		$query.= " (concat(date_start,'',time_start)) >= '$dbStartDateTime' AND (concat(date_start,'',time_start)) < '$dbEndDateTime'";

		$eventUserId = $currentUser->getId();
		$params = array_merge(array($eventUserId), $this->getGroupsIdsForUsers($eventUserId));

		$query.= " AND nectarcrm_crmentity.smownerid IN (".generateQuestionMarks($params).")";
		$query.= ' ORDER BY time_start';

		$queryResult = $db->pquery($query, $params);
		while ($record = $db->fetchByAssoc($queryResult)) {
			$item = array();
			$item['id']				= $record['activityid'];
			$item['visibility']		= $record['visibility'];
			$item['activitytype']	= $record['activitytype'];
			$item['status']			= $record['eventstatus'];
			$item['priority']		= $record['priority'];
			$item['userfullname']	= getUserFullName($record['smownerid']);
			$item['title']			= decode_html($record['subject']);

			$dateTimeFieldInstance = new DateTimeField($record['date_start'].' '.$record['time_start']);
			$userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
			$startDateComponents = explode(' ', $userDateTimeString);

			$item['start'] = $userDateTimeString;
			$item['startDate'] = $startDateComponents[0];
			$item['startTime'] = $startDateComponents[1];

			$dateTimeFieldInstance = new DateTimeField($record['due_date'].' '.$record['time_end']);
			$userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
			$endDateComponents = explode(' ', $userDateTimeString);

			$item['end'] = $userDateTimeString;
			$item['endDate'] = $endDateComponents[0];
			$item['endTime'] = $endDateComponents[1];

			if ($currentUser->get('hour_format') == '12') {
				$item['startTime'] = nectarcrm_Time_UIType::getTimeValueInAMorPM($item['startTime']);
				$item['endTime'] = nectarcrm_Time_UIType::getTimeValueInAMorPM($item['endTime']);
			}
			$recurringCheck = false;
			if($record['recurringtype'] != '' && $record['recurringtype'] != '--None--') {
				$recurringCheck = true;
			}
			$item['recurringcheck'] = $recurringCheck;
			$result[$startDateComponents[0]][] = $item;
		}

		$response = new nectarcrm_Response();
		$response->setResult($result);
		$response->emit();
	}

	public function addDays($datetime, $daysToAdd) {
		$datetime = strtotime($datetime);
		$secondsDelta = 24 * 60 * 60 * $daysToAdd;
		$futureDate = $datetime + $secondsDelta;
		return date("Y-m-d H:i:s", $futureDate);
	}

	protected function getGroupsIdsForUsers($userId) {
		vimport('~~/include/utils/GetUserGroups.php');

		$userGroupInstance = new GetUserGroups();
		$userGroupInstance->getAllUserGroups($userId);
		return $userGroupInstance->user_groups;
	}

}
