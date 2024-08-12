document.addEventListener("DOMContentLoaded", function () {

    const sections = document.querySelectorAll("#bsSpyContent > div");
    const navLinks = document.querySelectorAll("#bsSpyTarget > a");

    function setActiveLink(link) {
        navLinks.forEach((navLink) => navLink.classList.remove("active"));
        link.classList.add("active");
    }

    window.onscroll = () => {
        const scrollY = window.scrollY;

        sections.forEach((section) => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute("id");
            if (scrollY + 300 >= sectionTop - 300 && scrollY < sectionTop + sectionHeight) {
                setActiveLink(document.querySelector(`nav a[href*='${sectionId}']`));
            }
        });
    };

    const inputProtocol = document.querySelector('input[name="json-protocol"]');
    if (window.location.protocol === "http:") {
        inputProtocol.value = "http";
    } else if (window.location.protocol === "https:") {
        inputProtocol.value = "https";
    }

    let domain = window.location.hostname;

    const inputDomain = document.querySelector('input[name="json-domain"]');
    inputDomain.value = domain;

    const inputSessionName = document.querySelector('input[name="json-session-name"]');
    const SessionName = domain.split('.').slice(0, -1).join('.');
    inputSessionName.value = SessionName + '-session';

    let closeButtonsAlert = document.querySelectorAll(".btn-close");
    closeButtonsAlert.forEach(function (button) {
        button.addEventListener("click", function () {
            let alert = this.closest(".alert");
            if (alert) {
                alert.classList.remove("show");
                alert.classList.add("hide");
            }
        });
    });

    window.validateStep1 = function () {
        let complete = true;
        if (document.querySelector("input[name='json-company-name']").value === "Click on Edit") {
            complete = false;
        }

        if (document.querySelector("input[name='json-company-owner']").value === "Click on Edit") {
            complete = false;
        }

        if (document.querySelector("input[name='json-project-name']").value === "Click on Edit") {
            complete = false;
        }

        if (document.querySelector("input[name='json-license']").value === "Click on Edit") {
            complete = false;
        }

        if (document.querySelector("input[name='json-protocol']").value === "Click on Edit") {
            complete = false;
        }
        if (document.querySelector("input[name='json-domain']").value === "Click on Edit") {
            complete = false;
        }
        if (complete) {
            document.querySelector("input[name='step-1']").value = true
            document.getElementById("step-1-text").classList.remove("hide");
        }
    }

    window.validateStep2 = function () {
        let complete = true;
        if (document.querySelector("input[name='json-session-lifetime']").value === "Click on Edit") {
            complete = false;
        }
        if (document.querySelector("input[name='json-session-activity-expire']").value === "Click on Edit") {
            complete = false;
        }
        if (complete) {
            document.querySelector("input[name='step-2']").value = true
            document.getElementById("step-2-text").classList.remove("hide");
        }
    }

    window.validateStep3 = function () {
        let complete = true;
        if (document.querySelector("input[name='json-database-con1-db-name']").value === "Click on Edit") {
            complete = false;
        }
        if (document.querySelector("input[name='json-database-con1-db-host']").value === "Click on Edit") {
            complete = false;
        }
        if (document.querySelector("input[name='json-database-con1-db-user']").value === "Click on Edit") {
            complete = false;
        }
        if (document.querySelector("input[name='json-database-con1-db-pass']").value === "Click on Edit") {
            complete = false;
        }
        if (complete) {
            document.querySelector("input[name='step-3']").value = true
            document.getElementById("step-3-text").classList.remove("hide");
        }
    }

    function iconChange(inputSetting) {
        let iconElement = inputSetting.parentElement.querySelector("i");
        iconElement.classList.remove("text-body", "text-opacity-25", "text-danger");
        iconElement.classList.add("text-success");
    }

    function iconChangeError(inputSetting) {
        let iconElement = inputSetting.parentElement.querySelector("i");
        if (iconElement !== null) {
            iconElement.classList.remove("text-body", "text-opacity-25");
            iconElement.classList.add("text-danger");
        }
    }

    window.errorField = function (errorField) {
        errorField.classList.remove("hide");
        errorField.classList.add("show");
    }

    const modalCompanyName = new bootstrap.Modal(document.getElementById("modalCompanyName"));
    const saveChangesCompanyName = document.querySelector("button[name='b-save-company-name']");
    const inputSettingCompanyName = document.querySelector("input[name='i-company-name']");
    const errorFieldCompanyName = document.getElementById("a-company-name");
    const inputCompanyName = document.querySelector("input[name='json-company-name']");
    saveChangesCompanyName.addEventListener("click", function () {
        let companyName = inputSettingCompanyName.value;
        if (companyName.trim() === "") {
            errorField(errorFieldCompanyName);
        } else {
            inputCompanyName.value = companyName;
            iconChange(inputCompanyName);
            modalCompanyName.hide();
            window.validateStep1();
        }
    });

    const modalCompanyOwner = new bootstrap.Modal(document.getElementById("modalCompanyOwner"));
    const saveChangesCompanyOwner = document.querySelector("button[name='b-save-company-owner']");
    const inputSettingCompanyOwner = document.querySelector("input[name='i-company-owner']");
    const errorFieldCompanyOwner = document.getElementById("a-company-owner");
    const inputCompanyOwner = document.querySelector("input[name='json-company-owner']");
    saveChangesCompanyOwner.addEventListener("click", function () {
        let companyOwner = inputSettingCompanyOwner.value;
        if (companyOwner.trim() === "") {
            errorField(errorFieldCompanyOwner);
        } else {
            inputCompanyOwner.value = companyOwner;
            iconChange(inputCompanyOwner);
            modalCompanyOwner.hide();
            window.validateStep1();
        }
    });

    const modalProjectName = new bootstrap.Modal(document.getElementById("modalProjectName"));
    const saveChangesProjectName = document.querySelector("button[name='b-save-project-name']");
    const inputSettingProjectName = document.querySelector("input[name='i-project-name']");
    const errorFieldProjectName = document.getElementById("a-Project-name");
    const inputProjectName = document.querySelector("input[name='json-project-name']");
    saveChangesProjectName.addEventListener("click", function () {
        let companyOwner = inputSettingProjectName.value;
        if (companyOwner.trim() === "") {
            errorField(errorFieldProjectName);
        } else {
            inputProjectName.value = companyOwner;
            iconChange(inputProjectName);
            modalProjectName.hide();
            window.validateStep1();
        }
    });


    const modalLicense = new bootstrap.Modal(document.getElementById("modalLicense"));
    const saveChangesLicense = document.querySelector("button[name='b-save-license']");
    const inputSettingLicense = document.querySelector('input[name="i-license"]');
    const errorFieldLicense = document.getElementById("a-license");
    const errorFieldLicenseEval = document.getElementById("a-license2");
    const inputLicense = document.querySelector("input[name='json-license']");
    const checkboxLicense = document.querySelector('input[name="free-license"][type="checkbox"]');
    checkboxLicense.addEventListener('click', function () {
        if (checkboxLicense.checked) {
            inputSettingLicense.value = 'FREE-FREE-FREE-FREE';
            inputSettingLicense.setAttribute('readonly', 'true');
        } else {
            inputSettingLicense.value = '';
            inputSettingLicense.removeAttribute('readonly');
        }
    });
    saveChangesLicense.addEventListener("click", function () {
        let companyLicense = inputSettingLicense.value;

        if (companyLicense.trim() === "") {
            errorField(errorFieldLicense);
        } else {
            if (companyLicense.length !== 19) {
                errorField(errorFieldLicenseEval);
            } else {
                inputLicense.value = companyLicense;
                iconChange(inputLicense);
                modalLicense.hide();
                window.validateStep1();
            }
        }
    });

    const modalLang = new bootstrap.Modal(document.getElementById("modalLang"));
    const saveChangesLang = document.querySelector("button[name='b-save-lang']");
    const inputLang = document.querySelector("input[name='json-lang']");
    const inputMLang = document.querySelector("input[name='json-m-lang']");
    const inputSettingLang = document.querySelector('select[name="i-lang"]');
    const checkboxMLang = document.querySelector('input[name="multi-language"][type="checkbox"]');
    saveChangesLang.addEventListener("click", function () {
        inputLang.value = inputSettingLang.value;
        if (checkboxMLang.checked) {
            inputMLang.value = 'true'
        } else {
            inputMLang.value = 'false'
        }
        iconChange(inputLang);
        modalLang.hide();
        window.validateStep1();
    });

    const modalEntryView = new bootstrap.Modal(document.getElementById("modalEntryView"));
    const saveChangesEntryView = document.querySelector("button[name='b-save-entry']");
    const inputEntry = document.querySelector("input[name='json-entry']");
    const inputSettingEntry = document.querySelector('select[name="i-entry"]');
    saveChangesEntryView.addEventListener("click", function () {
        inputEntry.value = inputSettingEntry.value;
        iconChange(inputEntry);
        modalEntryView.hide();
        window.validateStep1();
    });

    const modalSessionLifeTime = new bootstrap.Modal(document.getElementById("modalSessionLifeTime"));
    const saveChangesLifeTime = document.querySelector("button[name='b-save-lifetime']");
    const inputLifeTime = document.querySelector("input[name='json-session-lifetime']");
    const inputSettingLifeTimeDays = document.querySelector('input[name="i-session-lifetime-days"]');
    const inputSettingLifeTimeHours = document.querySelector('input[name="i-session-lifetime-hours"]');
    saveChangesLifeTime.addEventListener("click", function () {
        let days = inputSettingLifeTimeDays.value || 0;
        let hours = inputSettingLifeTimeHours.value || 0;
        inputLifeTime.value = (parseInt(hours) * 60 * 60) + (parseInt(days) * 24 * 60 * 60);
        iconChange(inputLifeTime);
        modalSessionLifeTime.hide();
        window.validateStep2();
    });

    const modalSessionActivityExpire = new bootstrap.Modal(document.getElementById("modalSessionActivityExpire"));
    const saveChangesActivityExpire = document.querySelector("button[name='b-save-activity-expire']");
    const inputActivityExpire = document.querySelector("input[name='json-session-activity-expire']");
    const inputSettingActivityExpireDays = document.querySelector('input[name="i-session-activity-expire-days"]');
    const inputSettingActivityExpireHours = document.querySelector('input[name="i-session-activity-expire-hours"]');
    saveChangesActivityExpire.addEventListener("click", function () {
        let days = inputSettingActivityExpireDays.value || 0;
        let hours = inputSettingActivityExpireHours.value || 0;
        inputActivityExpire.value = (parseInt(hours) * 60 * 60) + (parseInt(days) * 24 * 60 * 60);
        iconChange(inputActivityExpire);
        modalSessionActivityExpire.hide();
        window.validateStep2();
    });

    const inputSettingDatabasePassword = document.querySelector('input[name="i-database-password"]');
    const meterSections = document.querySelectorAll('.meter-section');
    inputSettingDatabasePassword.addEventListener('input', updateMeter);

    function updateMeter() {
        const password = inputSettingDatabasePassword.value;
        const strength = calculatePasswordStrength(password);

        const strengthClasses = ['weak', 'medium', 'strong', 'very-strong'];

        meterSections.forEach((section, index) => {
            section.classList.remove(...strengthClasses);
            if (strength >= index + 1) {
                section.classList.add(strengthClasses[index]);
            }
        });
    }

    function calculatePasswordStrength(password) {
        const patterns = {
            uppercase: /[A-Z]/,
            lowercase: /[a-z]/,
            number: /\d/,
            symbol: /[^A-Za-z0-9]/
        };
        const weights = {
            length: 0.2,
            uppercase: 0.3,
            lowercase: 0.3,
            number: 0.5,
            symbol: 0.7
        };
        let strength = 0;
        for (const char of password) {
            for (const [pattern, regex] of Object.entries(patterns)) {
                if (regex.test(char)) {
                    strength += weights[pattern];
                    break;
                }
            }
        }
        return strength;
    }

    const testDBConnection = document.querySelector("button[name='test-bd-connection']");
    testDBConnection.addEventListener("click", function () {
        const inputElements = document.querySelectorAll('input[name^="i-database-"]');
        const form_data = new FormData();
        let error = false;
        var errorInput = [];
        inputElements.forEach(input => {
            let fieldName = input.name;
            let value = input.value;
            if (value === 'Click on Edit') {
                error = true;
                let labelFor = document.querySelector('label[for="' + input.id + '"]');
                errorInput.push(labelFor.textContent);
            }
            form_data.append(fieldName, value);
        });
        if (!error) {
            form_data.append("Data-Base-Test", "true");
            axios.post(window.location.href, form_data)
                .then(response => {
                    let field = document.getElementById("a-database-con1-connection-error");
                    if (response.data === true) {
                        field = document.getElementById("a-database-con1-connection-success");
                        document.querySelector("input[name='test-bd-connection-result']").value = 'true'
                        inputElements.forEach(input => {
                            input.setAttribute('readonly', 'true');
                            input.setAttribute('readonly', 'true');
                            input.setAttribute('readonly', 'true');
                            input.setAttribute('readonly', 'true');
                        });
                    }
                    window.errorField(field);
                })
                .catch(error => {
                    console.error(error);
                });
        } else {
            Swal.fire({
                icon: 'error',
                html: '<div class="text-start">For the platform to work correctly, you must fill out all the fields, otherwise you will not be able to continue.<br><br><b>Fields:</b><br>' + errorInput.join("<br>") + '</div>',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Return to Form'
            })
        }
    });


    const modalDataBase = new bootstrap.Modal(document.getElementById("modalDataBase"));
    const saveChangesDataBase = document.querySelector("button[name='b-save-database-con1']");


    const inputCon1DBName = document.querySelector("input[name='json-database-con1-db-name']");
    const inputCon1DBHost = document.querySelector("input[name='json-database-con1-db-host']");
    const inputCon1DBUser = document.querySelector("input[name='json-database-con1-db-user']");
    const inputCon1DBPass = document.querySelector("input[name='json-database-con1-db-pass']");
    const inputSettingDataBaseName = document.querySelector('input[name="i-database-name"]');
    const inputSettingDataBaseHost = document.querySelector('input[name="i-database-host"]');
    const inputSettingDataBaseUser = document.querySelector('input[name="i-database-user"]');
    const inputSettingDataBasePass = document.querySelector('input[name="i-database-password"]');

    saveChangesDataBase.addEventListener("click", function () {
        let error = false;
        let smg = '';
        let name = inputSettingDataBaseName.value;
        let host = inputSettingDataBaseHost.value;
        let user = inputSettingDataBaseUser.value;
        let pass = inputSettingDataBasePass.value;

        if (name === "") {
            error = true;
            smg = 'the DB Name field cannot be empty.';
        }

        if (host === "") {
            error = true;
            smg = 'the DB Name field cannot be empty.';
        }

        if (user === "") {
            error = true;
            smg = 'the DB Name field cannot be empty.';
        }

        if (pass === "") {
            error = true;
            smg = 'the DB Name field cannot be empty.';
        }

        if (pass.length < 8) {
            error = true;
            smg = 'the DB Password must have at least 8 characters.<br>Your account seems insecure, you should change your password before continuing.';
        }

        if (error) {
            Swal.fire({
                icon: 'error',
                html: '<div class="text-start">For the platform to work correctly, you must fill out all and correctly the fields, otherwise you will not be able to continue.<br><br><b>Error Detected:</b><br>' + smg + '</div>',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Return to Form'
            })
        } else {
            inputCon1DBName.value = inputSettingDataBaseName.value;
            inputCon1DBHost.value = inputSettingDataBaseHost.value;
            inputCon1DBUser.value = inputSettingDataBaseUser.value;
            inputCon1DBPass.value = inputSettingDataBasePass.value;
            iconChange(inputCon1DBName);
            iconChange(inputCon1DBHost);
            iconChange(inputCon1DBUser);
            iconChange(inputCon1DBPass);
            modalDataBase.hide();
            window.validateStep3();
        }
    });

    /**
     * Send
     */

    const saveChangesJsonSave = document.querySelector("button[name='b-save-json']");
    saveChangesJsonSave.addEventListener("click", function () {
        const inputElements = document.querySelectorAll('input[name^="json-"]');
        const form_data = new FormData();
        let error = false;
        var errorField = [];
        inputElements.forEach(input => {
            let fieldName = input.name;
            let value = input.value;
            if (value === 'Click on Edit') {
                error = true;
                let labelFor = document.querySelector('label[for="' + input.id + '"]');
                if (labelFor !== null) {
                    errorField.push(labelFor.textContent);
                    const inputTarget = document.querySelector("input[name='" + fieldName + "']");
                    iconChangeError(inputTarget);
                }
            }
            form_data.append(fieldName, value);
        });
        if (!error) {
            axios.post(window.location.href, form_data)
                .then(response => {
                    if (response.data) {
                        Swal.fire({
                            icon: 'success',
                            html: 'Installation completed successfully!!!',
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: 'Return to Form'
                        }).then(() => {
                            window.location.href = window.location.origin;
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            html: 'Installation error!!!',
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: 'Return to Form'
                        })
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        } else {
            Swal.fire({
                icon: 'error',
                html: '<div class="text-start">For the platform to work correctly, you must fill out all the fields, otherwise you will not be able to continue.<br><br><b>Fields:</b><br>' + errorField.join("<br>") + '</div>',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Return to Form'
            })
        }
    });
});