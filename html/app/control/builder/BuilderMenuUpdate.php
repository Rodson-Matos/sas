<?php

/**
 * BuilderMenuUpdate
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class BuilderMenuUpdate extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        if (TSession::getValue('login') !== 'admin') 
        {
            new TMessage('error',  _t('Permission denied'));
            return;
        }
    }

    /**
     * Ask for Update menu
     */
    public function onAskUpdate()
    {
        try 
        {
            if (!file_exists('menu-dist.xml')) 
            {
                throw new Exception(_t('File not found') . ':<br> menu-dist.xml');
            }

            $action = new TAction(array($this, 'onUpdateMenu'));
            new TQuestion(_t('Update menu overwriting existing file?'), $action);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Update menu
     */
    public static function onUpdateMenu($param)
    {
        try 
        {
            if (!file_exists('menu.xml') or !is_writable('menu.xml')) 
            {
                throw new Exception("Error Processing Request", 1);
            }

            $menuPath = 'menu.xml';
            copy('menu-dist.xml', $menuPath);

            $newMenu = BuilderPageService::getMenu();
            $menu    = new TMenuWriter($menuPath);
            $submoduleAdded = [];

            foreach ($newMenu as $module => $properties) 
            {
                $action = !empty($properties->action) ? $properties->action : '';
                $icon = !empty($properties->icon) ? $properties->icon : '';
                $color = !empty($properties->color) ? $properties->color : '';
                
                $menu->appendModule($module, $action, $icon, $color);

                if (empty($properties->items)) 
                {
                    continue;
                }

                foreach ($properties->items as $item) 
                {
                    if (empty($item->items)) 
                    {
                        $action = !empty($item->action) ? $item->action : '';
                        $icon   = !empty($item->icon) ? $item->icon : '';
                        $color  = !empty($item->color) ? $item->color : '';
                        $label  = !empty($item->label) ? $item->label : '';
                        
                        if(!$label && !empty($item->submodule))
                        {
                            $label = $item->submodule;
                        }
                        
                        if(!$color && !empty($item->icon_color))
                        {
                            $color = $item->icon_color;
                        }
                        
                        $menu->appendItem($module, $label, $action, $icon, $color);
                    } 
                    else 
                    {
                        foreach ($item->items as $sub_item) 
                        {
                            if (empty($submoduleAdded[$module][$item->submodule])) 
                            {
                                $submoduleAdded[$module][$item->submodule] = true;
                                
                                $color  = !empty($item->color) ? $item->color : '';
                                if(!$color && !empty($item->icon_color))
                                {
                                    $color = $item->icon_color;
                                }
                                $action = !empty($item->action) ? $item->action : '';
                                
                                $menu->appendSubModule($module, $item->submodule, $action, $item->icon, $color);
                            }
                            $menu->appendItem($sub_item->module, $sub_item->label, $sub_item->action, $sub_item->icon, $sub_item->icon_color, $item->submodule);
                        }
                    }
                }
            }

            $menu->save($menuPath);
            new TMessage('info', _t('Menu updated successfully'));
            TScript::create('setTimeout(function(){location.href = "index.php"}, 200);');
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
}