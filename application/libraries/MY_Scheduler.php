<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library for the CodeIgniter framework.
 *
 * @package        CodeIgniter
 * @version        1.0
 * @author        Kamaro Lambert <kamaroly@gmail.com> modified by Alta Falconeri <falconerialta@gmail.com>
 * @description CodeIgniter library to helpe you create scheduler tasks on windows
 * @copyright        Copyright (c) Sept 2013, Kamaro Lambert
 * @link        http://huguka.com/
 * @license             GPL/MIT
 * @example
 * $this->load->library('MY_scheduler');  //Loading library
 * $this->my_scheduler->create_task($name,$frequency,$program, $start_time, $end_time, $start_date,$end_date);  //Creating a new Scheduler tasks
 * $this->my_scheduler->delete_task($name); //Delete a scheduler tasks by it's name
 */
Class MY_Scheduler
{
    var $CI;

    function __construct()
    {

        $this->CI =& get_instance();
    }

    /**
     * @name         create_task ()
     * @method to add task schedule in windows using php
     *
     * @param String $frequency -Frequency in minute E.g 5
     * @param string $name -name of the Task E.g "My Tasks"
     * @param string $program - “C:\RunMe.bat”
     * @param string $start_time -Time in HH:mm (24-hour time) E.g 09:00
     * @param string $end_time -Time in HH:mm (24-hour time) E.g 18:00
     * @param string $start_date -Date dd/mm/yyyy E.g 01/01/2015
     * @param string $end_date -Date dd/mm/yyyy E.g 15/05/2015
     *
     * @example $this->schedule('DAILY','My Task','C:RunMe.bat',09:00,18:00,01/01/2015,15/05/2015);
     *
     */

    function create_task($name = null, $frequency = null, $program = null, $start_time = null, $end_time = null, $start_date = null, $end_date = null)
    {
        //$command = 'SchTasks /Create /SC MINUTE /MO ' . $frequency . ' /TN "' . $name . '" /TR "' . $program . '" /ST ' . $start_time . ' /ET ' . $end_time . '  /SD ' . $start_date . ' /ED ' . $end_date . ' /ru SYSTEM /Z';
        $command = 'SchTasks /Create /SC DAILY /MO 1 /RI ' . $frequency . ' /TN "' . $name . '" /TR "' . $program . '" /ST ' . $start_time . ' /ET ' . $end_time . ' /ru SYSTEM /K /Z';
        $output  = exec($command);
        if (strpos($output, 'success') != false) {
            $result = $this->modify_task($name, $frequency, $program, $start_time, $end_time, $start_date, $end_date);

            return $result;
        }
        else {
            return false;
        }


    }


     function create_task_hr($name = null, $frequency = null, $program = null, $start_time = null, $end_time = null, $start_date = null, $end_date = null)
    {
        //$command = 'SchTasks /Create /SC MINUTE /MO ' . $frequency . ' /TN "' . $name . '" /TR "' . $program . '" /ST ' . $start_time . ' /ET ' . $end_time . '  /SD ' . $start_date . ' /ED ' . $end_date . ' /ru SYSTEM /Z';
        $command = 'SchTasks /Create /SC DAILY /MO 1 /RI ' . $frequency . ' /TN "' . $name . '" /TR "' . $program . '" /ST ' . $start_time . ' /ET ' . $end_time . ' /ru SYSTEM /K /Z';
        $output  = exec($command);
        if (strpos($output, 'success') != false) {
            $result = $this->modify_task($name, $frequency, $program, $start_time, $end_time, $start_date, $end_date);

            return $result;
        }
        else {
            return false;
        }


    }

    /**
     * @method to modify existing tasks
     *
     * @param String $frequency -Frequency for which the program follow E.g DAILY,MONTHLY,WEEKLY
     * @param string $name -name of the Task E.g "My Tasks"
     * @param string $program - “C:\RunMe.bat”
     * @param time   $time -Time in Hour and minutes E.g 09:00
     * @param        $days -MON,TUE,WED,THU,FRI
     *
     * @example $this->schedule('My Task','DAILY','C:RunMe.bat',09:00);
     * @todo add option to use username and password
     * ******************************************************************************************
     */

    function modify_task($name = null, $frequency = null, $program = null, $start_time = null, $end_time = null, $start_date = null, $end_date = null)
    {
        $command = 'SchTasks /Change';

        if ($name != null) {
            $command .= ' /TN "' . $name . '"';
        }

        //Does user want to change the program to execute?
        if ($program != null) {
            $command .= ' /TR "' . $program . '" ';
        }

        //Does user want to the time?
        if ($frequency != null) {
            $command .= ' /RI ' . $frequency;
        }


        if ($start_time != null) {
            $command .= ' /ST ' . $start_time;
        }

        if ($end_time != null) {
            $command .= ' /ET ' . $end_time;
        }

        if ($start_date != null) {
            $command .= ' /SD ' . $start_date;
        }

        if ($end_date != null) {
            $command .= ' /ED ' . $end_date;
        }

        $command .= ' /Z';

        $output = exec($command);

        // var_dump($output);die();
        if (strpos(strtolower($output), 'success') !== false) {
            return true;
        }
        else {
            return false;
        }

    }

    /**
     * @method to delete task created
     * @name         delete_task ()
     *
     * @param  array $task_name -name of the Task E.g "My Tasks"
     *
     * @return boolean
     * * ***********************************************************
     */
    function delete_task($task_name = null)
    {
        $command = 'SchTasks /Delete /TN "' . $task_name . '" /F';

        //SchTasks /Delete /TN “My Task”
        $output = exec($command);

        if (strpos($output, 'success') !== false) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @method to modify existing tasks
     * @name         bulk_task_creation
     *
     * @param String $frequency -Frequency for which the program follow E.g DAILY,MONTHLY,WEEKLY
     * @param string $name -name of the Task E.g "My Tasks"
     * @param string $program - “C:\RunMe.bat”
     * @param time   $time -Time in Hour and minutes E.g 09:00
     * @param        $days -MON,TUE,WED,THU,FRI
     *
     * @example $this->bulk_task_creation($array());
     * @todo allow the scheduler to be created in bulk by passing an array
     * ******************************************************************************************
     */
    function bulk_task_creation()
    {
        //SchTasks /Create /SC DAILY /TN “Backup Data” /TR “C:Backup.bat” /ST 07:00
        //SchTasks /Create /SC WEEKLY /D MON /TN “Generate TPS Reports” /TR “C:GenerateTPS.bat” /ST 09:00
        //SchTasks /Create /SC MONTHLY /D 1 /TN “Sync Database” /TR “C:SyncDB.bat” /ST 05:00
    }

    /**
     * @method to modify existing tasks
     *
     * @param String $frequency -Frequency for which the program follow E.g DAILY,MONTHLY,WEEKLY
     * @param string $name -name of the Task E.g "My Tasks"
     * @param string $program - “C:\RunMe.bat”
     * @param time   $time -Time in Hour and minutes E.g 09:00
     * @param        $days -MON,TUE,WED,THU,FRI
     *
     * @example $this->schedule('DAILY','My Task','C:RunMe.bat',09:00);
     * ******************************************************************************************
     */
    function run_task($taskname)
    {

        //SCHTASKS /Run [/S <system> [/U <username> [/P [<password>]]]] /TN <taskname>

    }
}
