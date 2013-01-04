const int syscall_forbidden_table_cnt = 23;
const int syscall_forbidden_table[] = {
        0, //restart_syscall
        2, //fork
        14, //mknod
        21, //mount
        22, //oldumount
        37, //kill
        39, //mkdir
        40, //rmdir
        52, //umount
        88, //reboot
        92, //truncate
        93, //ftruncate
        113, //vm86old
        115, //swapoff
        120, //clone
        128, //init_module
        129, //delete_module
        149, //sysctl
        166, //vm86old
        190, //vfork
        193, //truncate64
        194, //ftruncate64
        238, //tkill
}; 
