<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\BaseValidations;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Payments
{
    use BaseValidations;

    /**
     * PMT.
     *
     * Returns the constant payment (annuity) for a cash flow with a constant interest rate.
     *
     * @param mixed $interestRate Interest rate per period
     * @param mixed $numberOfPeriods Number of periods
     * @param mixed $presentValue Present Value
     * @param mixed $futureValue Future Value
     * @param mixed $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PMT($interestRate = 0, $numberOfPeriods = 0, $presentValue = 0, $futureValue = 0, $type = 0)
    {
        $interestRate = Functions::flattenSingleValue($interestRate);
        $numberOfPeriods = Functions::flattenSingleValue($numberOfPeriods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = Functions::flattenSingleValue($futureValue);
        $type = Functions::flattenSingleValue($type);

        try {
            $interestRate = self::validateFloat($interestRate);
            $numberOfPeriods = self::validateInt($numberOfPeriods);
            $presentValue = self::validateFloat($presentValue);
            $futureValue = self::validateFloat($futureValue);
            $type = self::validateInt($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        // Calculate
        if ($interestRate != 0.0) {
            return (-$futureValue - $presentValue * (1 + $interestRate) ** $numberOfPeriods) /
                (1 + $interestRate * $type) / (((1 + $interestRate) ** $numberOfPeriods - 1) / $interestRate);
        }

        return (-$presentValue - $futureValue) / $numberOfPeriods;
    }

    /**
     * PPMT.
     *
     * Returns the interest payment for a given period for an investment based on periodic, constant payments
     *         and a constant interest rate.
     *
     * @param mixed $interestRate Interest rate per period
     * @param mixed $period Period for which we want to find the interest
     * @param mixed $numberOfPeriods Number of periods
     * @param mixed $presentValue Present Value
     * @param mixed $futureValue Future Value
     * @param mixed $type Payment type: 0 = at the end of each period, 1 = at the beginning of each period
     *
     * @return float|string Result, or a string containing an error
     */
    public static function PPMT($interestRate, $period, $numberOfPeriods, $presentValue, $futureValue = 0, $type = 0)
    {
        $interestRate = Functions::flattenSingleValue($interestRate);
        $period = Functions::flattenSingleValue($period);
        $numberOfPeriods = Functions::flattenSingleValue($numberOfPeriods);
        $presentValue = Functions::flattenSingleValue($presentValue);
        $futureValue = ($futureValue === null) ? 0.0 : Functions::flattenSingleValue($futureValue);
        $type = ($type === null) ? 0 : Functions::flattenSingleValue($type);

        try {
            $interestRate = self::validateFloat($interestRate);
            $period = self::validateInt($period);
            $numberOfPeriods = self::validateInt($numberOfPeriods);
            $presentValue = self::validateFloat($presentValue);
            $futureValue = self::validateFloat($futureValue);
            $type = self::validateInt($type);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($period <= 0 || $period > $numberOfPeriods) {
            return Functions::NAN();
        }

        // Calculate
        $interestAndPrincipal = new InterestAndPrincipal(
            $interestRate,
            $period,
            $numberOfPeriods,
            $presentValue,
            $futureValue,
            $type
        );

        return $interestAndPrincipal->principal();
    }
}
