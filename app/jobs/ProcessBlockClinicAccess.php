<?php


class ProcessBlockClinicAccess
{
    public function fire($job, $data)
    {
        $blocker = new \BlockClinicAccess($data);

        $blocker->execute();
    }
}