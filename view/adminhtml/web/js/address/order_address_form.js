define([
    'jquery',
    'mage/translate',
    'underscore',
    'uiComponent'
], function ($, $t, _, Component) {
    return Component.extend({
        initialize: function () {
            this.reloadAgency = false;
            this.agenciesUrl = window.saSpAgenciesUrl;

            this.reloadCommune = false;
            this.communesUrl =  window.saSpCommunesUrl;

            let self = this;

            setTimeout(function(){
                    self.bindCommuneAgencyRelation();
                    self.lastAgencyId = '';
                    let saPrevAgencyId = $('#sa_prev_agency_id');
                    if (saPrevAgencyId.length > 0) {
                        self.lastAgencyId = saPrevAgencyId.val();
                    }
                    self.hideAdditionalFields();
                    self.bindRegionCommuneRelation();
                    self.lastCommuneId = '';
                    let saPrevCommuneId = $('#sa_prev_commune_id');
                    if (saPrevCommuneId.length > 0) {
                        self.lastCommuneId = saPrevCommuneId.val();
                    }
                }, 3000
            );

            $('.sales-order-address #edit_form .field.city').hide();
            $('.sales-order-address #edit_form .field.field-commune').hide();
            $('.sales-order-address #edit_form .field.zip').hide();
            let saPrevRut = $('#sa_prev_rut');
            let saRut = $('#rut');
            if (saPrevRut.length > 0 && saRut.length > 0) {
                saRut.val(saPrevRut.val());
                saRut.addClass('validate-cl-rut');
            }

            this._super();

            return this;
        },
        beforeSave: function (data, url) {
            alert(data);
            this._super();
        },
        bindCommuneAgencyRelation: function() {
            var communeElement = $('.sales-order-address #edit_form select[name="commune_id"]').first();
            communeElement.change(this.reloadAgencyField.bind(this));
            this.initAgencyField(communeElement[0]);
        },
        initAgencyField: function(element) {
            var communeElement = element;
            if (communeElement && communeElement.id) {
                var agencyElement  = $('.sales-order-address #edit_form input[name="agency_id"]');
                if (agencyElement.length == 0) {
                    agencyElement  = $('.sales-order-address #edit_form select[name="agency_id"]');
                }
                if (agencyElement) {
                    this.agencyElement = agencyElement;
                    if (communeElement.value.length) {
                        var url = this.agenciesUrl;
                        //this.loader.load(url, {}, this.refreshAgencyField.bind(this));
                        $.ajax({
                            context: this,
                            url: url,
                            type: "POST",
                            dataType: "json",
                            data: {parent: communeElement.value},
                        }).done(function (data) {
                            this.refreshAgencyField(data);
                        });
                    } else {
                        this.clearAgencyField(this.agencyElement.disabled);
                    }
                }
            }
        },
        reloadAgencyField: function(event) {
            this.reloadAgency = true;
            var communeElement = event.target;
            if (communeElement && communeElement.id) {
                var agencyElement  = $('.sales-order-address #edit_form input[name="agency_id"]');
                if (agencyElement.length == 0) {
                    agencyElement  = $('.sales-order-address #edit_form select[name="agency_id"]');
                }
                if (agencyElement) {
                    this.agencyElement = agencyElement;
                    if (communeElement.value.length) {
                        var url = this.agenciesUrl;
                        //this.loader.load(url, {}, this.refreshAgencyField.bind(this));
                        $.ajax({
                            context: this,
                            url: url,
                            type: "POST",
                            dataType: "json",
                            data: {parent: communeElement.value},
                        }).done(function (data) {
                            this.refreshAgencyField(data);
                        });
                    } else {
                        this.clearAgencyField(this.agencyElement.disabled);
                    }
                }
            }
        },
        refreshAgencyField: function(serverResponse) {
            if (serverResponse) {
                var data = serverResponse;
                var lastValue = this.agencyElement[0].value;
                if (lastValue == '') {
                    lastValue = this.lastAgencyId;
                }
                var disabled = this.agencyElement.disabled;
                if (data.length) {
                    this.lastAgencyId = this.agencyElement.value
                    var select = document.createElement('select');
                    select.setAttribute('name', this.agencyElement[0].name);
                    select.setAttribute('title', this.agencyElement[0].title);
                    select.setAttribute('id', this.agencyElement[0].id);
                    select.setAttribute('class', 'admin__control-select');
                    if (disabled) {
                        select.setAttribute('disabled', '');
                    }
                    for (var i in data) {
                        if (data[i].label) {
                            var option = document.createElement('option');
                            option.setAttribute('value', data[i].value);
                            option.innerText = data[i].label;
                            if (data[i].value == '') {
                                option.innerText = $t('To send to Agency select an option');
                            }
                            if (this.agencyElement.value &&
                                (this.agencyElement.value == data[i].value || this.agencyElement.value == data[i].label)
                            ) {
                                option.setAttribute('selected', '');
                            } else if (lastValue !='' && data[i].value == lastValue) {
                                option.setAttribute('selected', '');
                            }
                            select.add(option);
                        }
                    }

                    if (this.agencyElement.parent() == null) {
                        this.agencyElement = $('.sales-order-address #edit_form #' + this.agencyElement.id).first();
                    }

                    var parentNode = this.agencyElement.parent();
                    var agencyElementId = "agency_id";
                    parentNode.html(select.outerHTML);
                    parentNode.parent().show();

                    this.agencyElement = $('.sales-order-address #edit_form select[name="' + agencyElementId + '"]').first();
                } else if (this.reloadAgency) {
                    this.clearAgencyField(disabled);
                }
            }
        },
        clearAgencyField: function(disabled) {
            var agencyElementId = "agency_id";
            this.agencyElement = $('.sales-order-address #edit_form select[name="' + agencyElementId + '"]');
            if (this.agencyElement.length == 0) {
                this.agencyElement = $('.sales-order-address #edit_form input[name="' + agencyElementId + '"]');
            }
            var text = document.createElement('input');
            text.setAttribute('type', 'text');
            text.setAttribute('name', this.agencyElement[0].name);
            text.setAttribute('title', this.agencyElement[0].title);
            text.setAttribute('id', this.agencyElement[0].id);
            text.setAttribute('class', 'admin__control-text');
            if (disabled) {
                text.setAttribute('disabled', '');
            }
            var parentNode = this.agencyElement.parent();
            parentNode.html(text.outerHTML);
            parentNode.parent().hide();
            this.agencyElement = $('.sales-order-address #edit_form input[name="' + agencyElementId + '"]').first();
        },
        bindRegionCommuneRelation: function() {
            var regionElement = $('.sales-order-address #edit_form select[name="region_id"]').first();
            regionElement.change(this.reloadCommuneField.bind(this));
            this.initCommuneField(regionElement[0]);
        },
        initCommuneField: function(element) {
            var regionElement = element;
            if (regionElement && regionElement.id) {
                var communeElement  = $('.sales-order-address #edit_form select[name="commune_id"]');
                if (communeElement) {
                    this.communeElement = communeElement;
                    if (regionElement.value.length) {
                        var url = this.communesUrl;
                        //this.loader.load(url, {}, this.refreshCommuneField.bind(this));
                        $.ajax({
                            context: this,
                            url: url,
                            type: "POST",
                            dataType: "json",
                            data: {parent: regionElement.value},
                        }).done(function (data) {
                            this.refreshCommuneField(data);
                        });
                    } else {
                        this.clearCommuneField(this.communeElement.disabled);
                    }
                }
            }
        },
        reloadCommuneField: function(event) {
            this.reloadCommune = true;
            var regionElement = event.target;
            if (regionElement && regionElement.id) {
                var communeElement  = $('.sales-order-address #edit_form select[name="commune_id"]');
                if (communeElement) {
                    this.communeElement = communeElement;
                    if (regionElement.value.length) {
                        var url = this.communesUrl;
                        //this.loader.load(url, {}, this.refreshCommuneField.bind(this));
                        $.ajax({
                            context: this,
                            url: url,
                            type: "POST",
                            dataType: "json",
                            data: {parent: regionElement.value},
                        }).done(function (data) {
                            this.refreshCommuneField(data);
                        });
                    } else {
                        this.clearCommuneField(this.communeElement.disabled);
                    }
                }
            }
        },
        refreshCommuneField: function(serverResponse) {
            if (serverResponse) {
                var data = serverResponse;
                var lastValue = this.communeElement[0].value;
                if (lastValue == '') {
                    lastValue = this.lastCommuneId;
                }
                var disabled = this.communeElement.disabled;
                if (data.length) {
                    var select = document.createElement('select');
                    select.setAttribute('name', this.communeElement[0].name);
                    select.setAttribute('title', this.communeElement[0].title);
                    select.setAttribute('id', this.communeElement[0].id);
                    select.setAttribute('class', 'admin__control-select');
                    if (disabled) {
                        select.setAttribute('disabled', '');
                    }
                    for (var i in data) {
                        if (data[i].label) {
                            var option = document.createElement('option');
                            option.setAttribute('value', data[i].value);
                            option.innerText = data[i].label;
                            if (data[i].value == '') {
                                option.innerText = $t('Please select a commune.');
                            }
                            if (this.communeElement.value &&
                                (this.communeElement.value == data[i].value || this.communeElement.value == data[i].label)
                            ) {
                                option.setAttribute('selected', '');
                            } else if (lastValue !='' && data[i].value == lastValue) {
                                option.setAttribute('selected', '');
                            }
                            select.add(option);
                        }
                    }

                    if (this.communeElement.parent() == null) {
                        this.communeElement = $('.sales-order-address #edit_form #' + this.communeElement.id).first();
                    }

                    var parentNode = this.communeElement.parent();
                    var communeElementId = "commune_id";
                    parentNode.html(select.outerHTML);

                    this.communeElement = $('.sales-order-address #edit_form select[name="' + communeElementId + '"]');

                    this.bindCommuneAgencyRelation();
                } else if (this.reloadCommune) {
                    this.clearCommuneField(disabled);
                }
                this.communeElement.change(this.updateAdditionalFields.bind(this));
            }
        },
        clearCommuneField: function(disabled) {
            var communeElementId = "commune_id";
            this.communeElement = $('.sales-order-address #edit_form select[name="' + communeElementId + '"]').first();
            if (this.communeElement.length == 0) {
                this.communeElement = $('.sales-order-address #edit_form input[name="' + communeElementId + '"]').first();
            }
            var select = document.createElement('select');
            select.setAttribute('name', this.communeElement[0].name);
            select.setAttribute('title', this.communeElement[0].title);
            select.setAttribute('id', this.communeElement[0].id);
            select.setAttribute('class', 'admin__control-select');
            if (disabled) {
                select.setAttribute('disabled', '');
            }
            var option = document.createElement('option');
            option.setAttribute('value', '');
            option.innerText = $t('Please select a commune.');
            select.add(option);
            var parentNode = this.communeElement.parent();
            parentNode.html(select.outerHTML);
        },
        updateAdditionalFields: function(event) {
            var communeElement = event.target;
            if (communeElement && communeElement.id) {
                var comSelIndex = communeElement.selectedIndex;
                var communeText = communeElement.options[comSelIndex].text;
                $('.sales-order-address #edit_form input[name="commune"]').val(communeText).change();
                $('.sales-order-address #edit_form input[name="city"]').val(communeText).change();
            }
        },
        hideAdditionalFields: function() {
            $('.sales-order-address #edit_form .field.city').hide();
            $('.sales-order-address #edit_form .field.field-commune').hide();
            $('.sales-order-address #edit_form .field.zip').hide();
        }
    });
});
