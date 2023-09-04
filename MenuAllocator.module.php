<?php namespace ProcessWire;

class MenuAllocator extends WireData implements Module {

    public static function getModuleInfo() {
        return array(
            'title' => 'Menu Allocator',
            'version' => 1,
            'summary' => 'A module to allocate menus to pages.',
            'autoload' => 'template=admin',
        );
    }

    public function init() {
        // Add a hook to build the page edit form content
        $this->addHookAfter('ProcessPageEdit::buildFormContent', $this, 'addCustomFieldToPageEditForm');
        
        // Add a hook to process the form input when the page is saved
        $this->addHookAfter('ProcessPageEdit::processInput', $this, 'saveCustomField');
    }

    public function addCustomFieldToPageEditForm(HookEvent $event) {
        $form = $event->return;

        // Check if this is a frontend page (you can define your criteria here)
        if (strpos($this->input->url, '/admin/') !== false) {
            // Create the custom field as an InputfieldText
            $field = $this->modules->get('InputfieldText');
            $field->name = 'custom_text_field'; // Use a different name for rendering
            $field->label = 'Custom Field for Display'; // Different label for rendering
            $field->description = 'Enter a custom value for this page (display only).';
    
            // Check if the page has a custom_text_field property and use it as the field's value
            if ($this->input->post->custom_text_field_property) {
                $field->value = $this->input->post->custom_text_field_property; // Use the property as the value
            }
    
            // Add the field to the page edit form
            $form->add($field);
        }
    }

    public function saveCustomField(HookEvent $event) {
        $form = $event->arguments(0);
    
        // We only care about the top-level form
        $level = $event->arguments(1);
        if ($level) return;
    
        // Get the custom_text_field value from the form input
        $fieldValue = $this->input->post->custom_text_field; // Use the correct field name
    
        // Debugging: Check if the hook is triggered and if the field value is retrieved correctly
        wire('log')->save('my-log-file.log', "Hook triggered. Field Value: $fieldValue");
    
        // Check if we have a valid page object
        if ($this->input->page) {
            // Debugging: Check if the page object is valid
            wire('log')->save('my-log-file.log', "Page object is valid.");
    
            // Save the field value as a property on the page with a different name
            $this->input->page->set('custom_text_field_property', $fieldValue); // Use the different property name
        }
    }
        
}
