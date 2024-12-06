/*
const config = HandlerModal.initModal();
config.title = 'Landscape Mode Required';
config.content = 'Please rotate your device to Landscape mode to continue.';
config.showTitleClose = false;
config.showFooterClose = false;
config.backdrop = 'static';
config.keyboard = false;
config.type = 'warning';
config.show = false;

const modalInstance = HandlerModal.createModal(config);

HandlerUtilities.aplicarOnChangeDeviceOrientation({
    onLandscape: () => {
        if (modalInstance._isShown) {
            modalInstance.hide();
        }
    },
    onPortrait: () => {
        if (!modalInstance._isShown) {
            modalInstance.show();
        }
    }
});

 */
