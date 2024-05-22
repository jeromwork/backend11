<?php

namespace App\Services\Infoclinic;

class PatientService
{
    protected ?string $firstName =  null;
    protected ?string $lastName = null;
    protected ?string $middleName = null;
    protected string $phone = '';
    protected ?int $birthdate = null;

    protected InfoclinicConnectService $infoclinic;

    public function __construct()    {
        $this->infoclinic = new InfoclinicConnectService();
    }

    public function getPatientId(): ?int {
        try {
            if( !$this->checkFillInfo() ) return null;

            $date = date('d.m.Y');
            $birthdate = (!$this->birthdate) ? "NULL": '\'' . date('d.m.Y', $this->birthdate) . '\'';

            $query = "SELECT PCODE FROM CF_CLIENTS_SEARCH(10000001, 1, 1, '',100, 1, 1, 2, 0, NULL, NULL, NULL, NULL, NULL, NULL,'$this->firstName','$this->middleName','$this->lastName',".$birthdate.", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 1, NULL,'$this->phone',NULL, NULL, 1, NULL,'$date',NULL, NULL, NULL, NULL)";
            if($patientInfo = $this->infoclinic->query($query)){
                if(isset($patientInfo['PCODE'])) return (int) $patientInfo['PCODE'];
            }
            return null;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }



    public function createPatient(): ?int {
        try {
            if($patientId = $this->getPatientId()) return $patientId;

            $birthdate = (!$this->birthdate) ? "NULL": '\'' . date('d.m.Y', $this->birthdate) . '\'';
            $ex = (!$this->birthdate) ? "NULL": '\'' . date('Y', $this->birthdate) . '\''; // year
            $doctorid = 10000253; //its stupid, always save to Каршев

            $query = "SELECT ERROR_CODE, ERROR_TEXT, PCODE FROM CF_CLIENTS_UPDATE($doctorid,1, 1, -1,'$this->lastName','$this->firstName','$this->middleName',NULL,NULL,NULL,NULL,'$this->phone',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,10159613,NULL,$ex,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 99, NULL, NULL, NULL, NULL,4363,NULL,NULL,NULL,NULL,NULL,NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1,  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,  NULL, NULL, NULL,  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0,  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)";

            if(!$patientInfo = $this->infoclinic->query($query)) throw new \Exception('Error create patient infoclinic');
            if(isset($patientInfo['ERROR_TEXT'])) throw new \Exception($patientInfo['ERROR_TEXT']);
            return (int) $patientInfo['PCODE'];

        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function withFirstName( string $firstName):self{
        $this->firstName = $firstName;
        return $this;
    }

    public function withLastName( string $lastName):self{
        $this->lastName = $lastName;
        return $this;
    }

    public function withMiddleName( string $middleName):self{
        $this->middleName = $middleName;
        return $this;
    }

    public function withPhone( string $phone):self{
        $this->phone = $phone;
        return $this;
    }

    public function withBirthDate( string $birthdate):self{
        $this->birthdate = strtotime($birthdate);
        return $this;
    }

    protected function checkFillInfo():bool{
        if( !$this->phone ) throw new \Exception('Not set phone');
        if( $this->firstName = null ) throw new \Exception('Not set firstName');
        if( $this->lastName = null ) throw new \Exception('Not set lastName');
        if( $this->middleName = null ) throw new \Exception('Not set middleName');
        if( $this->birthdate = null ) throw new \Exception('Not set birthdate');
        return true;
    }



}
