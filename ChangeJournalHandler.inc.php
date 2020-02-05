<?php

/**
 * @file plugins/generic/changejournal/ChangeJournalHandler.inc.php
 *
 * Copyright (c) 2018 - 2020 Stephen Sciberras
 * Distributed under the GNU GPL v2 or later. For full terms see the file docs/COPYING.
 *
 * @class ChangeeJournalHandler
 *
 * @brief Handle reader-facing router requests for the Change Journal plugin
 * does the dirty work of changing database entries
 */

import('classes.handler.Handler');
import('classes.article.ArticleDAO');

class ChangeJournalHandler extends Handler {

	/**
	 * @copydoc PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.ContextRequiredPolicy');
		$this->addPolicy(new ContextRequiredPolicy($request));

		import('classes.security.authorization.OjsJournalMustPublishPolicy');
		$this->addPolicy(new OjsJournalMustPublishPolicy($request));

		return parent::authorize($request, $args, $roleAssignments);
	}

	/**
	 * Change Journal for a submission: changes following DB - submission, notifications, stageAssignments
	 *
	 * @param $args array [
	 *		@option string Section ID
	 *		@option string page number
 	 * ]
	 * @param $request PKPRequest
	 * @return null|JSONMessage
	 */
	public function change($args, $request) {
    $submissionID = $request->getuservar('submissionId');
    $oldJournalID =$request->getuservar('journal');
    if ($oldJournalID == "mmsg") {
	    $oldJournalID = "1";
	} else {
	    $oldJournalID = "2";
	}
    
    $submissionDao = Application::getSubmissionDAO();
        
        //$oldJournalID = $request->getContext()->getId();
        error_log ( "********  form handler  ************");
        $dump = var_export ($oldJournalID, true);
        error_log ( $dump);
        
		$submission = $submissionDao->getById($submissionID, $oldJournalID, false); // IMPORTANT CHANGE 1 to context_id
        $stageID = $submission->getStageId();
		error_log ( "******** change form handler  ************");
    
		//Testing - setting variables through code until GUI is ready
		//$oldJournalID = $request->getContext()->getId();
		if ($oldJournalID == 1) {
		    $newJournalID = "2";
		}
		else {
		    $newJournalID = "1";
		}
	//	$newJournalID = "2";
		$newSectionID = "1";
    
		//Copy directors of article from one Journal to another
    //get path of this context
		//get path of new contextID
		import('lib.pkp.classes.file.ContextFileManager');  //Calls also FileManager
		$currentcontextFileManager = new ContextFileManager($oldJournalID);
		$currentBasePath = $currentcontextFileManager->getBasePath() . "/articles/" . $submissionID;

		$newcontextFileManager =  new ContextFileManager($newJournalID);
		$newBasePath = $newcontextFileManager->getBasePath() . "/articles/" . $submissionID;

    $fileManager = new FileManager();
    $fileManager->Copydir($currentBasePath, $newBasePath );
   
    //Adjust stageassignments
    // Get the stageID
    $stageID = $submission->getStageId();
    
    // First get all users assigned to this submission, including authors, reviewers, editors etc
    $stageAssignmentDao = DAORegistry::getDAO('StageAssignmentDAO');
    $stageAssignment = $stageAssignmentDao->getBySubmissionAndStageId($submissionID, $stageID)->Toarray();
    
    // Needed to get usergroup details, such as names, ID and get equivalent in the other journal
    $userGroupsDao = DAORegistry::getDAO('UserGroupDAO');
    // also, need to get user group names and role ID in the new journal
    $userGroupsNewJournal = $userGroupsDao->getByContextId( $newJournalID )->Toarray();
    
    //Then loop through all of these, to get their user group, look up the equivalent in the new Journal, and update
    foreach ($stageAssignment as $value)
    {
      $userGroupIDoldJournal = $value->getUserGroupId();
      
      $userGroups = $userGroupsDao->getById($userGroupIDoldJournal, $oldJournalID);
      $userGroupName = $userGroups->getLocalizedName();
      //find the equivalent user_group_id in the new Journal
      foreach ($userGroupsNewJournal as $equivalent)
      {
        if ($userGroupName == $equivalent->getLocalizedName())
        {
          // equivalent user group found...
          $equivalentUserGroupID = $equivalent->getData('id');
        }
      }
      
      //now update the usergroup to the new journal.
      $value->setUserGroupId($equivalentUserGroupID);
      $stageAssignmentDao->updateObject($value);
    }
    unset($value);
    
		// get the necessary variables: submissionID (reduntant?), new contextID, new SectionID. + ???
			$submission->setSectionId ( $newSectionID );
      $dump = var_export ($submission, true);
			error_log ( "******** new submission  ************");
			//error_log ( $dump);

    //Finally, update section and context id in the databases.
		$submissionDao->updateObject($submission);
		$submissionDao->update("UPDATE submissions SET context_id = ? WHERE submission_id = ?", array($newJournalID, $submissionID));
    // update notifications as well
    $ASSOC_TYPE_SUBMISSION = ASSOC_TYPE_SUBMISSION;
    error_log ( "******** $ASSOC_TYPE_SUBMISSION  ************");
    $submissionDao->update("UPDATE notifications SET context_id = ? WHERE assoc_type = $ASSOC_TYPE_SUBMISSION AND assoc_id = ?", array($newJournalID, $submissionID));
    
    // How to log this event?
    
	}
}

?>
