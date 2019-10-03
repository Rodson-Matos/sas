<?php
require_once 'init.php';

try 
{
    
    TTransaction::open('builder');
    
    
    $entities = DiagramEntity::where('diagram_id', '=', Diagram::PERMISSION)->load();
    
    foreach ($entities as $diagramEntity)
    {    
        $diagramEntity->inserts_data = '';
        $insertsData = json_decode($diagramEntity->inserts_data);
                    
        $vars = get_object_vars($insertsData);
        $newInsertData = new stdClass();
        
        echo "<br><br> OLD inserts_data: {$diagramEntity->inserts_data} <br><br><br>";
        
        foreach ($insertsData as $key => $insertData) 
        {
            if($key != 'varName')
            {
                TTransaction::open('builder');
                $entityColumn = EntityColumn::where('diagram_entity_id', '=', $diagramEntity->id)->where('name', '=', $key)->load();
                TTransaction::close();
                if($entityColumn)
                {
                    $entityColumn = $entityColumn[0];
                    $newInsertData->{$entityColumn->id} = $insertData;
                }
                else
                {
                    echo '<pre>';
                    var_dump("Não achou a coluna : {$key} | Diagrama:{$diagramEntity->id}");
                    unset($insertsData->{$key});
                    echo '</pre>';
                }
            }
            else
            {
                $newInsertData->{$key} = $insertData;
            }
        }
        
        $conn = TTransaction::get();
        $inserts_data_up = json_encode($newInsertData);
        $inserts_data_up = str_replace("'", '', $inserts_data_up);
        $conn->query("update diagram_entity set inserts_data = '{$inserts_data_up}' where id = {$diagramEntity->id}");
        
        echo "update diagram_entity set inserts_data = '{$inserts_data_up}' where id = {$diagramEntity->id} <br><br>";
    }    
    TTransaction::close();
} 
catch (Exception $e) 
{
    var_dump($e->getMessage());
    TTransaction::rollback();
}







