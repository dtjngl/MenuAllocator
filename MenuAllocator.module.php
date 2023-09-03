<?php namespace ProcessWire; 

class MenuAllocator extends WireData implements Module, ConfigurableModule {
    public static function getModuleInfo() {
        return array(
            'title' => 'Menu Allocator',
            'version' => 1,
            'summary' => 'A module to add a custom field to page edit forms.',
            'autoload' => 'template=admin',
        );
    }

    
    public function __construct() {
    
        $menuAllocatorSettings = wire('modules')->getConfig($this);
        foreach ($menuAllocatorSettings as $key => $value) {
            $this->$key = $value;
        }
    
    }


    public function init() {
        // Add a hook to build the page edit form
        $this->addHookAfter('ProcessPageEdit::buildForm', $this, 'addCustomFieldToPageEditForm');
        
        // Add a hook to save the field value when the page is saved
        $this->addHookAfter('Pages::saveReady', $this, 'saveCustomField');
    }

    
    public function addCustomFieldToPageEditForm(HookEvent $event) {

        $this->message('this is addCustomFieldToPageEditForm');

        $page = $event->object->getPage();
    
        // Check if this is a frontend page (you can define your criteria here)
        if (strpos($page->path, '/admin/') !== 0) {
            // Get the selected menu names from the module's settings

            $menuNames = $this->menus;

            if ($menuNames == '') return;

            $menuNames = explode(' ', $menuNames);
    
            // Create the custom field as an InputfieldAsmSelect
            $field = $this->modules->get('InputfieldCheckboxes');
            $field->name = 'menus';
            $field->label = 'Menus';
            $field->description = 'Select menu names for the page.';
    
            // Add menu options based on module configuration
            foreach ($menuNames as $menuName) {
                $field->addOption($menuName, $menuName);
            }
    
            // Add the field to the page edit form
            $form = $event->return;
            $form->insertBefore($field, $form->getChildByName('title')); // Adjust the position as needed
        }
    }


    public function saveCustomField(HookEvent $event) {
        $page = $event->arguments[0];
    
        // Check if this is a frontend page (you can define your criteria here)
        if (strpos($page->path, '/admin/') !== 0) {
            // Get the field value from the input
            $fieldValue = $this->input->post->menus; // Use the correct field name
    
            $this->message('Field value received: ' . print_r($fieldValue, true));
    
            // Create the dynamic field if it doesn't exist
            if (!$page->hasField('menus')) {
                $field = $this->wire('fields')->get('menus'); // Adjust 'menus' to your field settings
                $field->type = $this->modules->get('FieldtypeText'); // Adjust the field type as needed
                $page->fields->add($field);
            }
    
            // Save the field value to the page
            $page->set('menus', $fieldValue); // Use the correct field name
        }
    }
            
    
                  
}

?>