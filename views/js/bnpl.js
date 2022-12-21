var BNPL = function(conf) {

    this.validationController = conf['validationController'] === undefined ? null : conf['validationController'];

    this.paypal = conf['paypal'] === undefined ? null : conf['paypal'];

    this.messages = conf['messages'] === undefined ? [] : conf['messages'];
}

BNPL.prototype.render = function (container, order) {

    if (this.paypal === null) {
        return;
    }

    this.paypal.Buttons({

        fundingSource: this.paypal.FUNDING.PAYLATER,

        createOrder: function(data, actions) {
            return actions.order.create(order);
        },

        onApprove: function(data, actions) {
            return actions.order.capture()
                .then(function(detail) {
                    this.validateOrder(detail);
                }.bind(this))
        }.bind(this)
    }).render(container)
}

BNPL.prototype.validateOrder = function(detail) {
    if (this.validationController === null) {
        return;
    }

    var form = document.createElement('form');
    var input = document.createElement('input');

    input.name = "paymentData";
    input.value = JSON.stringify(detail);

    form.method = "POST";
    form.action = this.validationController;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}