<?php

/**
 * @file plugins/generic/changejournal/ChangeJournalHandler.inc.php
 *
 * Copyright (c) 2018 - 2020 Stephen Sciberras
 * Distributed under the GNU GPL v2 or later. For full terms see the file docs/COPYING.
 *
 * @class ChangeeJournalFormHandler
 *
 * @brief Handle reader-facing router requests for the Change Journal plugin
 */

import('classes.handler.Handler');
import('classes.article.ArticleDAO');

class ChangeJournalFormHandler extends Handler {

	/**
	 * @copydoc PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		$dump = var_export ($args, true);
		error_log ( $dump);
		error_log ( "********  TESTING ************");
		//import('lib.pkp.classes.security.authorization.ContextRequiredPolicy');
		//$this->addPolicy(new ContextRequiredPolicy($request));

		//import('classes.security.authorization.WorkflowStageAccessPolicy');
		//	$this->addPolicy(new WorkflowStageAccessPolicy($request));

    	// Authorize requested submission.
		import('lib.pkp.classes.security.authorization.internal.SubmissionRequiredPolicy');
		$this->addPolicy(new SubmissionRequiredPolicy($request, $args, 'submissionId'));

		// This policy will deny access if user has no accessible workflow stage.
		// Otherwise it will build an authorized object with all accessible
		// workflow stages and authorize user operation access.
		import('lib.pkp.classes.security.authorization.internal.UserAccessibleWorkflowStageRequiredPolicy');
		$this->addPolicy(new UserAccessibleWorkflowStageRequiredPolicy($request, WORKFLOW_TYPE_EDITORIAL));


		// Users are assigned to specific stages of a submission's
		// workflow. Check access to the stage, not the submission.
		// In this example, the stage id is submitted as a query
		// parameter with the request.
		$stageId = $request->getUserVar('stageId');
		$dump = var_export ($stageId, true);
		error_log ( $dump);

		// The submission ID should be submitted as a query
		// parameter. Tell the policy what parameter to check for
		// the submission id. In this example, we assume the URL
		// used for the request included ?submissionId=1
		$queryParam = 'submissionId';

		import('lib.pkp.classes.security.authorization.WorkflowStageAccessPolicy');
		$this->addPolicy(new WorkflowStageAccessPolicy($request, $args, $roleAssignments, $queryParam, $stageId));
		//import('lib.pkp.classes.security.authorization.WorkflowStageAccessPolicy');
		//$this->addPolicy(new WorkflowStageAccessPolicy($request, $args, $roleAssignments, 'submissionId', $this->identifyStageId($request, $args), WORKFLOW_TYPE_EDITORIAL));

		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array(
				'fetch',
				'loadForm',
			)
		);

		return parent::authorize($request, $args, $roleAssignments);
	}

  public function loadForm($args, $request, $path) {
		error_log ( "********  TESTING ************");
		return false;
	}
	/**
	 * Initialize form for modal
	 * path is ChangeJournalForm.tpl
	 */
  public function fetch($args, $request, $path) {
		//$submissionID = $request->getuservar('submissionId');
		error_log ( "********  FORMHANDLER fecth test issue.tpl ************");
		//$templateMgr = TemplateManager::getManager($request);

		//$test = $templateMgr->fetch($path);
		$dump = var_export ($args, true);
		error_log ( $dump);
		error_log ( "********  FORMHANDLER Fetch json message  ************");

return "<h1>testing</h1>";
return $templateMgr->fetchJson($path);
		return new JSONMessage(true, $templateMgr->fetch($path));

		$args[0] = "plugins.generic.changejournal.modals.ChangeJournalForm";
		import($args[0]);
		$testing = new ChangeJournalForm(6, null, $path);
		$templateMgr = TemplateManager::getManager($request);

		$testing->fetch($request, $path, true);
		error_log ( "********  FORMHANDLER from fetch  ************");

		return "<h1>testing</h1>"; //true; // $templateMgr->fetchjson($path);
	}
}

?>
