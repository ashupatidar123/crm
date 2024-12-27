<?php

if(!function_exists('printr')) {
    function printr($data,$type='multi'){
        if(!empty($data) && $type == 'multi'){
            echo '<pre>';
            print_r($data->toArray());
            echo '<pre>';
        }else{
            echo '<pre>';
            print_r($data);
            echo '<pre>';
        }
        exit;
    }
}

if(!function_exists('user_title')) {
    function user_title($val=''){
        $option = 
        '<option value="Mr.">Mr.</option>
        <option value="Mrs.">Mrs.</option>
        <option value="Miss.">Miss.</option>
        <option value="Dr.">Dr.</option>
        <option value="Prof.">Prof.</option>
        <option value="Engg.">Engg.</option>
        <option value="Caption">Caption</option>';

        return $option;
    }
}

?>