<?php namespace Phaza\LaravelPostgis\Geometries;

use GeoIO\WKB\Parser\Parser;
use Phaza\LaravelPostgis\Exceptions\UnknownWKTTypeException;

abstract class Geometry implements GeometryInterface, \JsonSerializable
{
    protected static $wkb_types = [
        1 => 'Phaza\LaravelPostgis\Geometries\Point',
        2 => 'Phaza\LaravelPostgis\Geometries\LineString',
        3 => 'Phaza\LaravelPostgis\Geometries\Polygon',
        4 => 'Phaza\LaravelPostgis\Geometries\MultiPoint',
        5 => 'Phaza\LaravelPostgis\Geometries\MultiLineString',
        6 => 'Phaza\LaravelPostgis\Geometries\MultiPolygon',
        7 => 'Phaza\LaravelPostgis\Geometries\GeometryCollection',
    ];

    public static function getWKTArgument($value)
    {
        $left = strpos($value, '(');
        $right = strrpos($value, ')');

        return substr($value, $left + 1, $right - $left - 1);
    }

    public static function getWKTClass($value)
    {
        $left = strpos($value, '(');
        $type = trim(substr($value, 0, $left));

        switch (strtoupper($type)) {
            case 'POINT':
                return self::$wkb_types[1];
            case 'LINESTRING':
                return self::$wkb_types[2];
            case 'POLYGON':
                return self::$wkb_types[3];
            case 'MULTIPOINT':
                return self::$wkb_types[4];
            case 'MULTILINESTRING':
                return self::$wkb_types[5];
            case 'MULTIPOLYGON':
                return self::$wkb_types[6];
            case 'GEOMETRYCOLLECTION':
                return self::$wkb_types[7];
            default:
                throw new UnknownWKTTypeException('Type was ' . $type);
        }
    }

    public static function fromWKB($wkb)
    {
        $parser = new Parser(new Factory());

        return $parser->parse($wkb);
    }

    public static function fromWKT($wkt)
    {
        $wktArgument = static::getWKTArgument($wkt);

        return static::fromString($wktArgument);
    }
}
