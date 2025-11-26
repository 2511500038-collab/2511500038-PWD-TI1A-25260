<?php
function bersihkan($str)
{
    return htmlspecialchars(trim($str));
}

function tidakKosong($str)
{
    return strlen(bersihkan($str)) > 0;
}