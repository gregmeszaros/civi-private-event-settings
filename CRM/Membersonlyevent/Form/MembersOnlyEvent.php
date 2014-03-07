<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Membersonlyevent_Form_MembersOnlyEvent extends CRM_Event_Form_ManageEvent {
  function buildQuickForm() {
     
    // add form elements
    $this->add(
      'checkbox', // field type
      'is_members_only_event', // field name
      'Is members only event?', // field label
      '',   // list of attributes
      false // is required
    );
    
    // add form elements
    $this->add(
      'select', // field type
      'contribution_page_id', // field name
      'Contribution page used for membership signup', // field label
      $this->getContributionPagesAsOptions(),   // list of attributes
      false // is required
    );
    
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }
  
  /**
   * This function sets the default values for the form.
   *
   * @access public
   */
  function setDefaultValues() {
      
    $defaults = array();
    
    // Check for default values if any set defaults
    $BAO = new CRM_Membersonlyevent_BAO_MembersOnlyEvent();
    $params = array();
    $params['event_id'] = $this->_id;
    
    $query = $BAO->retrieve($params);
    
    if(count($query) > 0) {
      // If we have the ID, edit operation will fire
      $params['id'] = key($query);
      
      $defaults['is_members_only_event'] = $query[$params['id']]->is_members_only_event;
      $defaults['contribution_page_id'] = $query[$params['id']]->contribution_page_id;
    }
    
    return $defaults;
  }
  
  function getContributionPagesAsOptions() {
      
    $contribution_pages = CRM_Contribute_BAO_ContributionPage::commonRetrieveAll('CRM_Contribute_DAO_ContributionPage');
    
    $return_array = array();
    $return_array['NULL'] = ts('- Select contribution page -');
    
    foreach ($contribution_pages as $key => $contribution_object) {
      $return_array[$contribution_object['id']] = $contribution_object['title'];
    }
    
    return $return_array;
  }

  function postProcess() {
    $passed_values = $this->exportValues();
    
    $BAO = new CRM_Membersonlyevent_BAO_MembersOnlyEvent();
    $params = array();
    $params['event_id'] = $passed_values['id'];
    
    $query = $BAO->retrieve($params);
    
    if(count($query) > 0) {
      // If we have the ID, edit operation will fire
      $params['id'] = key($query);
    }
    
    $params['contribution_page_id'] = $passed_values['contribution_page_id'];
    $params['is_members_only_event'] = isset($passed_values['is_members_only_event']) ? $passed_values['is_members_only_event'] : 0;
        
    $BAO->create($params);
    
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
