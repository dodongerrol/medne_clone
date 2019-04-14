<select id="doctor-procedures">
    <option value="" >Select a Procedure</option>
    <?php if($loadarray['doctor_procedure']){
    foreach($loadarray['doctor_procedure'] as $doctorProcedure){
        $duration = $doctorProcedure->Duration.' '.$doctorProcedure->Duration_Format;
        echo '<option duration="'.$duration.'" value="'.$doctorProcedure->ProcedureID.'">'.$doctorProcedure->Name.'</option>'; 
    }
}?>
</select>