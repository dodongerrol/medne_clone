<?php

class GenerateExcelUrls
{
    protected $baseUrl = 'https://mednefits.s3-ap-southeast-1.amazonaws.com';
    protected $status;

    // Plans
    const OUT_OF_POCKET = 'out_of_pocket';

    public function __construct(array $status)
    {
        $this->status = $status;
    }

    public function getUrls(): array
    {
        $urls = [
            'employee' => "{$this->baseUrl}/excel/v5/myr/basic/employee/Employee+NO+SA.xlsx",
            'dependent'	=> "{$this->baseUrl}/excel/v5/myr/basic/dependent/Employees-and-Dependents+NO-SA.xlsx"
        ];

        switch ($this->status['account_type']) {
            case self::OUT_OF_POCKET:
                $urls = $this->outOfPocket($urls);
                break;
            /**
             * More use cases here
             * It's advisable to extract long conditions under each plans
             */
            default:
                // Silence is golden
                break;
        }

        return $urls;
    }

    /**
     * Conditions under out of pocket plan
     */
    protected function outOfPocket(array $urls): array
    {
        if (
            ($this->status['medical_enabled'] && $this->status['wellness_enabled']) &&
            ($this->status['medical_benefits_coverage']  != self::OUT_OF_POCKET && $this->status['wellness_benefits_coverage'] != self::OUT_OF_POCKET)
        ) {
            return [
                'employee' => "{$this->baseUrl}/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R.xlsx",
                'dependent'	=> "{$this->baseUrl}/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-All-R.xlsx"
            ];
        }

        if (
            ($this->status['medical_enabled'] && $this->status['medical_benefits_coverage'] != self::OUT_OF_POCKET)
        ) {
            return [
                'employee' => "{$this->baseUrl}/excel/v5/myr/basic/employee/Employee+SA+-+All+-+R+-+Medical.xlsx",
                'dependent'	=> "{$this->baseUrl}/excel/v5/myr/basic/dependent/Employees-and-Dependents+SA-R-Medical.xlsx"
            ];
        }

        if (
            ($this->status['wellness_enabled'] && $this->status['wellness_benefits_coverage'] != self::OUT_OF_POCKET)
         ) {
            return [
                'employee' => "{$this->baseUrl}/excel/v5/myr/enterprise/employee/Employee+R-Wellness.xlsx",
                'dependent'	=> "{$this->baseUrl}/excel/v5/myr/enterprise/dependent/Employees-and-Dependents+R-Wellness+.xlsx"
            ];
        }

        if (
            ($this->status['wellness_enabled'])
         ) {
            return [
                'employee' => "{$this->baseUrl}/excel/v5/myr/enterprise/employee/Employee-Wellness.xlsx",
                'dependent'	=> "{$this->baseUrl}/excel/v5/myr/enterprise/dependent/Employees-and-Dependents-Wellness.xlsx"
            ];
        }

        return $urls;
    }
}