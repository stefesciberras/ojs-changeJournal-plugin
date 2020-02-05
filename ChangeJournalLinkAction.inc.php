<?php
/**
 * @file plugins/generic/changejournal/ChangeJournalLinkAction.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Copyright (c) 2019 Stephen Sciberras
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ChangeJournalLinkAction
 *
 * @brief An action to open a modal to display metadata relevant to form to change the Journal of a submission.
 */

import('lib.pkp.classes.linkAction.LinkAction');
class ChangeJournalLinkAction extends LinkAction {

	/**
	 * Constructor
	 * @param $request Request
	 * @param $submissionId integer The submission to change.
   * @param $image string
	 */
	function __construct($request,  $image = 'information') {
		// Instantiate the modal.
    $router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');

		$actionArgs = array();
		$modal = new AjaxModal(

		$router->url($request, null, 'changeJournal', 'fetch', null, array('userid' => $userid)),
			__('changeJournal.modalTitle'),
			'modal_more_info'
		);

		// Configure the link action.
		$toolTip = ($image == 'completed') ? __('grid.action.galleyInIssueEntry') : null;
		parent::__construct('issueEntry', $modal, __('submission.issueEntry'), $image, $toolTip);
	}
	/*
	function nstruct($request, $submissionId, $reviewAssignmentId) {
		// Instantiate the meta-data modal.
		$dispatcher = $request->getDispatcher();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$modal = new AjaxModal(
				$dispatcher->url($request, ROUTE_COMPONENT, null,
						'modals.submissionMetadata.ReviewerSubmissionMetadataHandler',
						'fetch', null, array('submissionId' => $submissionId, 'reviewAssignmentId' => $reviewAssignmentId)),
				__('reviewer.step1.viewAllDetails'), 'modal_information');

		// Configure the link action.
		parent::__construct('viewMetadata', $modal, __('reviewer.step1.viewAllDetails'));
	}
	*/
}

?>
