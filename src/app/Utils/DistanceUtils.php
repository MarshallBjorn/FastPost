<?php

namespace App\Utils;

class DistanceUtils
{
    /**
     * Calculate the distance between two points on Earth using the Haversine formula.
     *
     * @param float $lat1 Latitude of the first point
     * @param float $lon1 Longitude of the first point
     * @param float $lat2 Latitude of the second point
     * @param float $lon2 Longitude of the second point
     * @return float Distance in kilometers, rounded to 2 decimal places
     */

    public static function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) *
                pow(sin($lonDelta / 2), 2)
        ));

        return round($angle * $earthRadius, 2); // Round to 2 decimal places
    }
}
