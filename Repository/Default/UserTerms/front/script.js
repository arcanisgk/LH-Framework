const checkbox = document.getElementById('acceptAll');
const acceptButton = document.getElementById('acceptButton');

if (checkbox && acceptButton) {
    checkbox.addEventListener('change', function () {
        acceptButton.disabled = !this.checked;
    });
}

document.querySelectorAll('.section-link').forEach(link => {
    link.addEventListener('click', async (e) => {
        const icon = e.target;
        const section = e.target.dataset.section;
        const tabPane = e.target.closest('.tab-pane');
        const tabId = tabPane.id;

        let policyType;
        switch (tabId) {
            case 'terms':
                policyType = 'Terms-of-Service';
                break;
            case 'privacy':
                policyType = 'Privacy-Policy';
                break;
            case 'cookies':
                policyType = 'Cookie-Policy';
                break;
            case 'humanitarian':
                policyType = 'Humanitarian-Data-Framework';
                break;
        }

        const url = `${window.location.origin}/User-Terms/Section=${policyType}#${section}`;

        try {
            await navigator.clipboard.writeText(url);

            // Add visual feedback
            icon.classList.add('copied');
            icon.classList.remove('fa-link');
            icon.classList.add('fa-check');

            // Reset after animation
            setTimeout(() => {
                icon.classList.remove('copied');
                icon.classList.remove('fa-check');
                icon.classList.add('fa-link');
            }, 1000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    });
});


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