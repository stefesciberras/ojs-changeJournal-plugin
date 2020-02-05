<?php 
error_log ( "********  FORM  ************");

// Import the base Form.
import('lib.pkp.classes.form.Form');
class ChangeJournalForm extends Form {
  
  	/** @var Context */
	var $context;

	/** @var int the ID of the submission */
	var $submissionId;

	/** @var Submission current submission */
	var $submission;
	


	/**
	 * Constructor.
	 * @param $submission object
	 'controllers/modals/submissionMetadata/form/submissionMetadataViewForm.tpl'
	 * @param $step int
	 */
	function __construct($context, $submission=null, $templatePath) {
		parent::__construct( $templatePath );
		error_log ( "********  FORM templatepath 3 ************");
		$this->submission = $submission;
		$this->submissionId = 193;
		$this->context = 6;
	}

	/**
	 * Fetch the form.
	 */
	function fetch($request, $template = null, $display = false)  {
		error_log ( "******** FORM fetch  ************");
    
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('submissionId', 193);
		$templateMgr->assign('submitStep', 1);

		return parent::fetch($request, $template, $display);
	}
  
  /*
function fetch($request) {
		$submission = $this->getSubmission();
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign(array(
			'submissionId' =>$submission->getId(),
			'stageId' => $this->getStageId(),
			'formParams' => $this->getFormParams(),
		));

		// Tell the form what fields are enabled (and which of those are required)
		import('lib.pkp.controllers.grid.settings.metadata.MetadataGridHandler');
		$context = $request->getContext();
		foreach (array_keys(MetadataGridHandler::getNames()) as $field) {
			$templateMgr->assign(array(
				$field . 'Enabled' => $context->getSetting($field . 'EnabledWorkflow'),
				$field . 'Required' => $context->getSetting($field . 'Required')
			));
		}
		// Provide available submission languages. (Convert the array
		// of locale symbolic names xx_XX into an associative array
		// of symbolic names => readable names.)
		$supportedSubmissionLocales = $context->getSetting('supportedSubmissionLocales');
		if (empty($supportedSubmissionLocales)) $supportedSubmissionLocales = array($context->getPrimaryLocale());
		$templateMgr->assign(
			'supportedSubmissionLocaleNames',
			array_flip(array_intersect(
				array_flip(AppLocale::getAllLocales()),
				$supportedSubmissionLocales
			))
		);
		return parent::fetch($request);
	}
  
  */
}
?>