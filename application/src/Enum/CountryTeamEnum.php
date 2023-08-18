<?php

namespace App\Enum;

/**
 * This needs because in the test case I don't use db,
 * it is good idea to have it in the db.
*/
enum CountryTeamEnum: string
{
    case BRAZIL = 'Brazil';
    case GERMANY = 'Germany';
    case ITALY = 'Italy';
    case ARGENTINA = 'Argentina';
    case FRANCE = 'France';
    case ENGLAND = 'England';
    case SPAIN = 'Spain';
    case NETHERLANDS = 'Netherlands';
    case URUGUAY = 'Uruguay';
    case SWEDEN = 'Sweden';
    case CZECH_REPUBLIC = 'Czech Republic';
    case HUNGARY = 'Hungary';
    case PORTUGAL = 'Portugal';
    case CROATIA = 'Croatia';
    case BELGIUM = 'Belgium';
    case CANADA = 'Canada';
    case AUSTRIA = 'Austria';
    case MEXICO = 'Mexico';
    case SWITZERLAND = 'Switzerland';
    case POLAND = 'Poland';
    case DENMARK = 'Denmark';
    case TURKEY = 'Turkey';
    case NORWAY = 'Norway';
    case ROMANIA = 'Romania';
}