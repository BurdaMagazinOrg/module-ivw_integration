if (window.innerWidth < ivw_mobile_width) {
    iam_data.cp = ivw_cpm;
    if(ivw_mobile_site) {
        iam_data.st = ivw_mobile_site;
    }
}
if ((typeof window.iom !== 'undefined') && (typeof window.iom.c === 'function')) {
    iom.c(iam_data, 1);
}
