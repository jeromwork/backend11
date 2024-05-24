<?php

namespace Modules\Reviews\Observers;

use Modules\Doctors\Models\Doctor;

class DoctorObserver
{
    /**
     * Handle the DoctorObserver "created" event.
     */
    public function created(Doctor $doctor): void
    {
        //
    }

    /**
     * Handle the DoctorObserver "updated" event.
     */
    public function updated(Doctor $doctor): void
    {
        //
    }

    /**
     * Handle the DoctorObserver "deleted" event.
     */
    public function deleted(Doctor $doctor): void
    {
        //
    }

    /**
     * Handle the DoctorObserver "restored" event.
     */
    public function restored(Doctor $doctor): void
    {
        //
    }

    /**
     * Handle the DoctorObserver "force deleted" event.
     */
    public function forceDeleted(Doctor $doctor): void
    {
        //
    }
}
