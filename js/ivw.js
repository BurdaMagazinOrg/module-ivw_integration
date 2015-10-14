if (window.innerWidth < ivw_mobile_width) {
    iam_data.cp = ivw_cpm;
}
if ((typeof window.iom !== 'undefined') && (typeof window.iom.c === 'function')) {
    iom.c(iam_data, 1);
}
