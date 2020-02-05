<?php

/**

* place tpl in this folder

 register the plugin

* Handler is done.
* Interface: place a link in the submission page, then call an AJAX modal


function redirectUrl($url)
	function redirect($context = null, $page = null, $op = null, $path = null, $params = null, $anchor = null) {


*/

import('lib.pkp.classes.plugins.GenericPlugin');

class changeJournalPlugin extends GenericPlugin {
  /**
   * Register the plugin.
   * @param $category string
   * @param $path string
   */
  function register($category, $path) {
    if (parent::register($category, $path)) {
      if ($this->getEnabled()) {
        HookRegistry::register('TemplateManager::display', array($this, 'callbackHandleDisplay'));
        HookRegistry::register('LoadHandler', array($this, 'loadPageHandler'));
        HookRegistry::register('LoadComponentHandler', array($this, 'callbackLoadHandler'));
        //HookRegistry::register('LoadComponentHandler', array($this, 'loadFormHandler'));
      }
      return true;
    }
    return false;
  }

  /**
	 * @copydoc PKPPlugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.changeJournal.displayName');
	}

	/**
	 * @copydoc PKPPlugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.changeJournal.description');
	}



  /**
	 * @copydoc Plugin::getTemplatePath()
	 */
	function getTemplatePath($inCore = false) {
		return parent::getTemplatePath($inCore) . 'templates/';
	}

  /**
	 * @copydoc Plugin::getTemplatePath()
	 */
	function getBasePath($inCore = false) {
		return parent::getTemplatePath($inCore);
	}

  /*
   * inject filter to be used during workflow tpl
   * This function will insert the trigger to open the dialog
   */
  function callbackHandleDisplay($hookName, $params) {
    $request = Application::getRequest();
	  $router = $request->getRouter();
    $templateMgr = $params[0];
    if ($params[1] ===  "workflow/workflow.tpl") {
    	$templateMgr->register_outputfilter(array($this, 'registrationFilter'));
    }
    return false;
  }

  /*
   * find match in the template to be displayed and add the trigger
   * TO CHECK WHY DUPLICATE
   */
  function registrationFilter($output, $templateMgr) {
    $pattern = '/<div id="submissionHeaderDiv" [\s\S]+?>/';

    if (preg_match($pattern, $output, $matches, PREG_OFFSET_CAPTURE))
    {
      $match = $matches[0][0];
      $offset = $matches[0][1];
    	// html up to matched pattern
      $newOutput = substr($output, 0, $offset);

    	$request = Application::getRequest();
    	import( 'plugins.generic.changejournal.ChangeJournalLinkAction');
      $templateMgr->assign(
      	'ChangeJournalAction',
      	new ChangeJournalLinkAction($request)
      );

   	  $newOutput .= $templateMgr->fetch($this->getTemplatepath() . "addLinkToChange.tpl");
    	$newOutput .= substr($output, $offset);
    	$output = $newOutput;
    }
    $templateMgr->unregister_outputfilter('registrationFilter');
    return $output;
  }

  /*
   * TO CHECK ChangeJournal has '6'
   * THIS IS NOT ACTIVE!!!!
   */
  function callbackLoadHandler ($hookName, $args) {
    $dump = var_export ($args, true);
    error_log ( "********  callbackloadhandler  ************");
    error_log ( $dump);
    if ($args[0] === "workflow" && $args[1] === "fetch") {
      error_log ( "********  callbackloadhandler  MATCHED ************");
      $args[0] = "plugins.generic.changejournal.modals.ChangeJournalForm";
      import($args[0]);
      $testing = new ChangeJournalForm(6, null, $this->getTemplatePath().'ChangeJournalForm.tpl');
      $request = Application::getRequest();
      $testing->fetch($request);
      return true;
    }
    return false;
  }


// THIS IS THE ACTIVE PART
  public function loadPageHandler($hookName, $args) {
    $dump = var_export ($args, true);
    error_log ( "********  loadpagehandler  ************");
    error_log ( $dump);
    $page = $args[0];
    // STEP 1
    // User has clicked the link to open the modal.
    if ($this->getEnabled() && $page === 'changeJournal'  && $args[1] === 'fetch') {
      /*
      * Dialog is to open, and load a form with Submission Data, and options to change
      */
      $this->import('ChangeJournalFormHandler');
      define('HANDLER_CLASS', 'ChangeJournalFormHandler');
      error_log ( "********  ********  PLUGIN PAGEHANDLER FN 1 ************  ************");
      $newForm = new ChangeJournalFormHandler;
      $request = Application::getRequest();

      //return new JSONMessage(true, $templateMgr->fetch('controllers/grid/issues/issue.tpl'));
      $newForm->fetch ($args, $request, $this->getTemplatePath().'ChangeJournalForm.tpl');
      return true;
    }

    // STEP 2
    if ($this->getEnabled() && $page === 'changeJournal' && $args[1] === 'change') {
      // the following grabs the request sent, and directs it to Handler to process the changes
      error_log ( "********  calling ChangeJournalHandler  ************");
      $this->import('ChangeJournalHandler');
      define('HANDLER_CLASS', 'ChangeJournalHandler');
      return true;
    }
    return false;
	}

  /*/ the following grabs the request sent, and directs it to Handler to process the changes
  public function loadFormHandler($hookName, $args) {

    $dump = var_export ($args, true);
    error_log ( "********  load formhandler  ************");
    error_log ( $dump);
		$page = $args[0];
		if ($this->getEnabled() && $page === 'plugins.generic.changejournal.ChangeJournalFormHandler' && $args[1] === 'fetch' ){
			error_log ( "********  PLUGIN FORMHANDER FN 1 ************");
			$this->import('ChangeJournalFormHandler');
			define('HANDLER_CLASS', 'ChangeJournalFormHandler');
			error_log ( "********  test2  ************");
			$test = new ChangeJournalFormHandler;
			$test->fetch ($args, $request, $this->getTemplatePath().'ChangeJournalForm.tpl');

			return true;
		}
		return false;
	}
  */

}
?>
