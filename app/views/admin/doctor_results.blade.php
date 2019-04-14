<?php if($doctorlist){
    echo '<option value="">select</option>';
    foreach($doctorlist as $docList){    
        echo '<option value="'.$docList->DoctorID.'">'.$docList->DocName.'</option>';
    }
}