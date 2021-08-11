<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 1/6/2015
 * Time: 9:58 AM
 */

if (! function_exists('make_compare')) {
    function make_compare()
    {
        // Normalize criteria up front so that the comparer finds everything tidy
        $criteria = func_get_args();
        foreach ($criteria as $index => $criterion) {
            $criteria[$index] = is_array($criterion) ? array_pad($criterion, 3, null) : array($criterion,
                                                                                              SORT_ASC,
                                                                                              null);
        }

        return function ($first, $second) use (&$criteria) {
            foreach ($criteria as $criterion) {
                // How will we compare this round?
                list($column, $sortOrder, $projection) = $criterion;
                $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

                // If a projection was defined project the values now
                if ($projection) {
                    $lhs = call_user_func($projection, $first[$column]);
                    $rhs = call_user_func($projection, $second[$column]);
                }
                else {
                    $lhs = $first[$column];
                    $rhs = $second[$column];
                }

                // Do the actual comparison; do not return if equal
                if ($lhs < $rhs) {
                    return -1 * $sortOrder;
                }
                else if ($lhs > $rhs) {
                    return 1 * $sortOrder;
                }
            }

            return 0; // tiebreakers exhausted, so $first == $second
        };
    }
}

if (! function_exists('convert_indonesia_date')) {
    function convert_indonesia_date($date)
    {
        $BulanIndo = array("Januari",
                           "Februari",
                           "Maret",
                           "April",
                           "Mei",
                           "Juni",
                           "Juli",
                           "Agustus",
                           "September",
                           "Oktober",
                           "November",
                           "Desember");

        $format = array('Sun' => 'Minggu',
                        'Mon' => 'Senin',
                        'Tue' => 'Selasa',
                        'Wed' => 'Rabu',
                        'Thu' => 'Kamis',
                        'Fri' => 'Jumat',
                        'Sat' => 'Sabtu',
                        'Jan' => 'Januari',
                        'Feb' => 'Februari',
                        'Mar' => 'Maret',
                        'Apr' => 'April',
                        'May' => 'Mei',
                        'Jun' => 'Juni',
                        'Jul' => 'Juli',
                        'Aug' => 'Agustus',
                        'Sep' => 'September',
                        'Oct' => 'Oktober',
                        'Nov' => 'November',
                        'Dec' => 'Desember');

        $tahun = substr($date, 6, 4);
        $bulan = substr($date, 3, 2);
        $tgl   = substr($date, 0, 2);
        $day   = strtr(date('D', strtotime($date)), $format);

        $result = $day . ' ' . $tgl . " " . $BulanIndo[(int)$bulan - 1] . " " . $tahun;

        return ($result);
    }
}

if (! function_exists('convert_indonesia_month')) {
    function convert_indonesia_month($month)
    {

        $format = array('January' => 'Januari',
                        'February' => 'Februari',
                        'March' => 'Maret',
                        'April' => 'April',
                        'May' => 'Mei',
                        'June' => 'Juni',
                        'July' => 'Juli',
                        'August' => 'Agustus',
                        'September' => 'September',
                        'October' => 'Oktober',
                        'November' => 'November',
                        'December' => 'Desember');
        $month  = strtr($month, $format);

        return ($month);
    }
}