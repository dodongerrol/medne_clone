<?php

class ProcessBlockClinicTypeAccess
{
    public function fire($job, $data)
    {
        $blocker = new \BlockClinicTypeAccess($data);

        $blocker->execute();
        $job->delete();
    }
}
