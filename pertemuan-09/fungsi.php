<?php
function bersihkan($str)
{
    return htmlspecialchars(trim($str));
}

function tidakKosong($str)
{
    return strlen(bersihkan($str)) > 0;
}

function formatTanggal($tanggal)
{
    return date("d M Y", strtotime($tgl));
}