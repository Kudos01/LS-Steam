<?php

namespace SallePW\SlimApp\Utilities;
use SallePW\SlimApp\Utilities\SessionUtilities;

class TwigSessionExtension {

    public function user_id() : int
    {
        return SessionUtilities::getSession();
    }

    public function picture_uuid() : String
    {
        return SessionUtilities::getPicture();
    }
}