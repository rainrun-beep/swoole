<?php
function sig_handler($sig)
{
    var_dump($sig);
    echo "信号操作\n";
}


pcntl_signal(SIGIO, "sig_handler");
posix_kill(posix_getpid(), SIGIO);

echo "kl\n";

pcntl_signal_dispatch();
// while (true) {
//   // code...
// }

