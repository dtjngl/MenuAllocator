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


    public function ready() {
        // Add a hook to build the page edit form content
        $this->addHookAfter('ProcessPageEdit::buildFormContent', $this, 'RenderMenusCheckboxes');
        
        // Add a hook to process the form input when the page is saved
        $this->addHook('Pages::saveReady', $this, 'saveSelectedMenus');

    }

    public function init() {

        // silence is golden :D

    }

    public function __construct() {
    
        $MenuAllocatorSettings = wire('modules')->getConfig($this);
        foreach ($MenuAllocatorSettings as $key => $value) {
            $this->$key = $value;
        }

        if ($this->ma_menus != '') {
            $this->ma_menus_array = explode(' ', $this->ma_menus);            
        }

    }

    public function RenderMenusCheckboxes(HookEvent $event) {

        $id = $this->input->get('id');
        $page = wire('pages')->get($id);

        $form = $event->return;
    
        // Check if this is a frontend page (you can define your criteria here)
        if (strpos($this->input->url, '/admin/') !== false) {
            // Create the ma_menus field as an InputfieldCheckboxes
            $field = $this->modules->get('InputfieldCheckboxes');
            $field->name = 'ma_menus'; // Use a different name for rendering
            $field->label = 'Menus'; // Different label for rendering
            $field->description = 'Select the menus where this page should be listed.';
    
            if (!$this->ma_menus_array) return;
            
            foreach ($this->ma_menus_array as $key => $value) {
                // Add each option to the $checkboxOptions array dynamically
                $checkboxOptions[$key] = $value;
            }
                
            // Check if the page's meta data has the ma_menus and set the selected options
            if ($page && $page->meta && $page->meta->has('ma_menus')) {
                $selectedOptions = $page->meta->get('ma_menus');
                $field->set('value', $selectedOptions);
            }
    
            // Set the options for the checkboxes
            $field->options = $checkboxOptions;
    
            // Add the field to the page edit form
            $form->add($field);
        }
    }
    
    public function saveSelectedMenus(HookEvent $event) {
        // Get the page object from ProcessWire's API
        $page = $event->arguments(0);
    
        // Get the ma_menus value from the form input
        $fieldValue = $this->input->post->ma_menus; // Use the correct field name
    
        // Reset the ma_menus value to an empty array
        $page->meta->set('ma_menus', []);
        $page->meta->set('ma_menus', $fieldValue); // Use the correct field name for meta data
    
        if (is_array($fieldValue)) {
            // Create an array to store the corresponding labels
            $menuLabels = [];
            foreach ($fieldValue as $value) {
                $menuLabels[] = $this->ma_menus_array[$value];
            }
            // Set the meta property for menu labels
            $page->meta->set('ma_menus_labels', $menuLabels);
        } else {
            // If $fieldValue is not an array, unset the ma_menus_labels
            $page->meta->remove('ma_menus_labels');
        }

    }
    

    public function getMenusArray(Page $page) {

        $ma_menus_array = [];
        
        foreach ($page->meta->get("ma_menus") as $key => $value) {
            $ma_menus_array[] = $this->ma_menus_array[$value];
        }

        return $ma_menus_array;

    }

    public function getPageArray(string $menu) {
        $ma_pages_array = [];
    
        $selector = "parent.id=1";
    
        foreach ($this->wire('pages')->find($selector) as $item) {
            if (!$item) continue;
            
            // Check if the page has the desired menu label
            if ($item->meta->get('ma_menus_labels') && in_array($menu, $item->meta->get('ma_menus_labels'))) {
                $ma_pages_array[] = $item;
            }
        }
    
        return $ma_pages_array;
    }
                    

}
