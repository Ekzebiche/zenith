 $( document ).ready(function() {
    var fontAwesome = false;

    // Путь к картинкам, если не подулючен к сайту fontawesome
    if (!fontAwesome) {
        var callFormIcon = '<img src="http://citizenfor.ru/zenith/smartlid/img/phone.svg" height="16">';
        var requestFormIcon = '<img src="http://citizenfor.ru/zenith/smartlid/img/envelope.svg" height="16">';
        var basketFormIcon = '<img src="http://citizenfor.ru/zenith/smartlid/img/phone.svg" height="16">';
    }

    // Подключение формы обратного звонка
    // true - выводить формы, false - не выводить
    var callForm = true;

    // Подключение формы с возможностью отправить сообщение
    // true - выводить формы, false - не выводить
    var requestForm = true;

    // Подключение корзины к landing page (пока не реализовано)
    // true - выводить формы, false - не выводить
    var basketForm = false;

    // Внешний вид форм (стилевое оформление) -- 'dark-space'
    var styleForm = 'dark-space';

    // Заголовок формы для заказа обратного звонка
    var callFormTitle = "Закажите обратный звонок, и наш консультант свяжется с вами";
    // Путь к иконке формы для заказа обратного звонка
    var callFormImg = '<img src="smartlid/img/call.svg">';

    // Заголовок формы для отправки заявки (сообщения)
    var requestFormTitle = "Оставьте заявку, и наш консультант свяжется с вами";
    // Путь к иконке формы для отправки заявки (сообщения)
    var requestFormImg = '<img src="smartlid/img/mail.svg">';

    // Заголовок формы с корзиной покупок (не реализовано)
    //var basketFormTitle = "Корзина";

    // placeholder для ввода имени
    var placeHolderName = "Введите ваше имя";

    //Обязательно ли поле с именем для заполнения? true - да, false - нет;
    var requiredName = true;

    // placeholder для ввода телефона
    var placeHolderTel = "Введите ваш телефон";

    //Обязательно ли поле с телефоном для заполнения? true - да, false - нет;
    var requiredTel = true;

    // placeholder для ввода почты
    var placeHolderMail = "Введите ваш email";

    //Обязательно ли поле с вводом почты для заполнения? true - да, false - нет;
    var requiredMail = true;

    // placeholder для ввода сообщения
    var placeHolderText = "Введите сообщение";
    //Обязательно ли поле с вводом текста/комментарием для заполнения? true - да, false - нет;
    var requiredText = false;

    // Конец настроек

    var quantityElements = callForm + requestForm + basketForm;
    var sourse = document.referrer;

    var view = {

        displayButton: function(div) {
            document.body.appendChild(div);
        },

        displayOverlay: function(overlay) {
            document.body.appendChild(overlay);
        },

        displayCallForm: function(div) {
            document.body.appendChild(div);
        },

        displayRequestForm: function(div) {
            document.body.appendChild(div);
        },

        displayAnswerForm: function(data, message, formTitle) {
            message.html(data);
            console.log(data);
            formTitle.css("display", "none");
            setTimeout(function() {
                formTitle.css("display", "block");
                message.html('');
                console.log(data);
            }, 3000);
        }
    };

    var controller = {
        sendForm: function() {
            $('.sl-form').submit(function() {
                // Получение ID формы
                var formID = $(this).attr('id');
                // Добавление решётки к имени ID
                var formNm = $('#' + formID);
                var message = $(formNm).find(".msgs");
                var formTitle = $(formNm).find(".form-title--title");
                $.ajax({
                    type: "POST",
                    url: 'http://citizenfor.ru/zenith/main/send_email',
                    data: formNm.serialize(),
                    success: function(data) {
                        // Вывод сообщения об успешной отправке или ошибке на стороне клиента
                        view.displayAnswerForm(data, message, formTitle);
                    },
                    error: function(jqXHR, text, error) {
                        // Вывод сообщения об ошибке отправки на стороне сервера
                        $('.msgs').html(error);
                        $('.formTitle').css("display", "none");
                        setTimeout(function() {
                            $('.formTitle').css("display", "block");
                            $('.msgs').html('');
                        }, 3000);
                    },
                    complete: function(data) {
                        $('input').not(':input[type=submit], :input[type=hidden]').val('');
                        $('textarea').val('');
                    }
                });
                return false;
            });
        },
        setFormInfo: function() {
            $(".sl-form--button").click(function() {
                $("input[name*='formInfo']").val($(this).attr("title"));
            });
        },

        setFormReferer: function() {
            $(".sl-form--button").click(function() {
                $("input[name*='formReferer']").val(sourse);
            })
        }
    };

    var model = {
        fa: fontAwesome,
        cf: callForm,
        rf: requestForm,
        rft: requestFormTitle,
        bf: basketForm,
        style: styleForm + ' sl-form--wrapper',


        createButton: function() {

            var div = document.createElement("div");
            var input = document.createElement("input");
            var label = document.createElement("label");
            var ul = document.createElement("ul");
            var li = document.createElement('li');
            var a = document.createElement("a");
            var span1 = document.createElement("span");
            var span2 = document.createElement("span");
            var span3 = document.createElement("span");

            div.appendChild(input);
            div.appendChild(label);
            div.appendChild(ul);
            div.className = "smartLidBtn";
            div.setAttribute('id', 'smartLidBtn');
            div.setAttribute('class', 'smartLidBtn');
            input.setAttribute('id', 'toggle-input');
            input.setAttribute('class', 'toggle-input');
            input.setAttribute('type', 'checkbox');
            label.setAttribute('for', 'toggle-input');
            if(this.fa){
                label.setAttribute('class', 'toggle');
            } else {
                label.setAttribute('class', 'toggle-img');
            }
            span1.setAttribute('class', 'rings');
            span2.setAttribute('class', 'rings');
            span3.setAttribute('class', 'rings');
            label.appendChild(span1);
            label.appendChild(span2);
            label.appendChild(span3);
            ul.setAttribute('class', 'btn-list');

            for (var i = 1; i <= quantityElements; i++) {
                var li = document.createElement('li');
                var a = document.createElement("a");
                ul.appendChild(li);
                li.appendChild(a);
                if (this.cf && i == 1) {
                    li.setAttribute('class', 'callForm');
                    a.setAttribute('href', '#sl-overlay-cf');
                    if (this.fa) {
                        a.innerHTML = '<i class="fa fa-phone" aria-hidden="true"></i>';
                    } else {
                        a.innerHTML = callFormIcon;
                        a.setAttribute('class', 'call-form--link');
                    }
                    var item = 'sl-overlay-cf';
                    this.createOverlay(item);
                    continue;
                }
                if ((this.rf && i == 2 && this.cf) || (this.rf && this.cf == false && i == 1)) {
                    li.setAttribute('class', 'requestForm');
                    a.setAttribute('href', '#sl-overlay-rf');
                    if (this.fa) {
                        a.innerHTML = '<i class="fa fa-envelope-o" aria-hidden="true"></i>';
                    } else {
                        a.innerHTML = requestFormIcon;
                        a.setAttribute('class', 'call-form--link');
                    };
                    var item = 'sl-overlay-rf';
                    this.createOverlay(item);
                    continue;
                }
                if ((this.bf && i == 3 && this.rf && this.cf) || (this.bf && this.rf == false && this.cf == false && i == 1) || (this.bf && (this.cf == false || this.rf == false) && i == 2)) {
                    li.setAttribute('class', 'basketForm');
                    a.setAttribute('href', '#sl-overlay-bf');
                    if (this.fa) {
                        a.innerHTML = '<i class="fa fa-shopping-basket" aria-hidden="true"></i>';
                    } else {
                        a.innerHTML = basketFormIcon;
                        a.setAttribute('class', 'call-form--link');
                    }
                    var item = 'sl-overlay-bf';
                    this.createOverlay(item);
                    break;
                }
            }
            view.displayButton(div);
        },

        createOverlay: function(item) {
            var overlay = document.createElement("div");
            overlay.setAttribute('id', item);
            overlay.setAttribute('class', 'sl-overlay');

            switch (item) {
                case 'sl-overlay-cf':
                    overlay.appendChild(this.createCallForm());
                    break;
                case 'sl-overlay-rf':
                    overlay.appendChild(this.createRequestForm());
                    break;
                case 'sl-overlay-bf':
                    overlay.appendChild(this.createBasketForm());
                    break;
            }

            view.displayOverlay(overlay);

        },

        createRequestForm: function() {
            if (this.rf) {
                var div = document.createElement('div');
                var p = document.createElement('p');
                var form = document.createElement('form');
                var formTitle = document.createElement('div');
                var inpName = document.createElement('input');
                var inpMail = document.createElement('input');
                var inpText = document.createElement('textarea');
                var formInfo = document.createElement('input');
                var formReferer = document.createElement('input');
                var formBtn = document.createElement('button');
                var formTitleIcon = document.createElement('div');
                var formTitleText = document.createElement('div');
                var a = document.createElement('a');
                var msgs = document.createElement('div');
                var copyright = document.createElement('p');
                var copyrightLink = document.createElement('a');

                div.appendChild(form);
                form.appendChild(formTitle);
                formTitle.appendChild(formTitleIcon);
                formTitle.appendChild(formTitleText);
                formTitleText.appendChild(p);
                p.innerHTML = requestFormTitle;
                p.setAttribute('class', 'form-title--title');
                form.appendChild(msgs);
                msgs.setAttribute('class', 'msgs');

                form.appendChild(inpName);
                form.appendChild(inpMail);
                form.appendChild(inpText);
                form.appendChild(formInfo);
                form.appendChild(formReferer);
                form.appendChild(formBtn);
                form.appendChild(a);
                div.appendChild(copyright);
                copyright.appendChild(copyrightLink);


                a.setAttribute('class', 'sl-overlay--close');
                a.setAttribute('href', '#modal-close');
                a.innerHTML = '×';

                div.setAttribute('class', this.style);
                form.setAttribute('method', 'POST');
                form.setAttribute('id', 'secondForm');
                form.setAttribute('class', 'sl-form');
                form.setAttribute('autocomplete', 'off');
                formTitleIcon.setAttribute('class', 'form-title--icon');
                formTitleIcon.innerHTML = requestFormImg;
                formTitleText.setAttribute('class', 'form-title--text');
                inpName.setAttribute('type', 'text');
                inpName.setAttribute('name', 'uname');
                inpName.setAttribute('class', 'sl-form--input');
                inpName.setAttribute('placeholder', placeHolderName);
                if(requiredName) {
                  inpName.setAttribute('required', 'required');
                }

                inpMail.setAttribute('type', 'email');
                inpMail.setAttribute('name', 'uemail');
                inpMail.setAttribute('placeholder', placeHolderMail);
                inpMail.setAttribute('class', 'sl-form--input');
                if(requiredMail) {
                  inpMail.setAttribute('required', 'required');
                }
                inpText.setAttribute('type', 'text');
                inpText.setAttribute('name', 'utext');
                inpText.setAttribute('class', 'sl-form--input');
                inpText.setAttribute('placeholder', placeHolderText);
                if(requiredText) {
                  inpText.setAttribute('required', 'required');
                }
                formInfo.setAttribute('type', 'hidden');
                formInfo.setAttribute('name', 'formInfo');
                formReferer.setAttribute('type', 'hidden');
                formReferer.setAttribute('name', 'formReferer');
                formBtn.setAttribute('class', 'sl-form--button');
                formBtn.setAttribute('title', 'Заявка с сайта');
                formBtn.setAttribute('type', 'submit');
                copyright.setAttribute('class', 'copyright-wprapper');
                copyrightLink.setAttribute('href', 'http://smartlanding.biz/smartlid');
                copyrightLink.innerHTML = "SmartLid";
                formBtn.innerHTML = 'Отправить';

                view.displayRequestForm(div);
                controller.setFormInfo();
                controller.setFormReferer();
                controller.sendForm();
                return div;
            };
        },

        createCallForm: function() {
            if (this.cf) {
                var div = document.createElement('div');
                var p = document.createElement('p');
                var form = document.createElement('form');
                var formTitle = document.createElement('div');
                var inpName = document.createElement('input');
                var inpTell = document.createElement('input');
                var formInfo = document.createElement('input');
                var formReferer = document.createElement('input');
                var formBtn = document.createElement('button');
                var formTitleIcon = document.createElement('div');
                var formTitleText = document.createElement('div');
                var a = document.createElement('a');
                var msgs = document.createElement('div');
                var copyright = document.createElement('p');
                var copyrightLink = document.createElement('a');

                div.appendChild(form);
                form.appendChild(formTitle);
                formTitle.appendChild(formTitleIcon);
                formTitle.appendChild(formTitleText);
                formTitleText.appendChild(p);
                p.innerHTML = callFormTitle;
                p.setAttribute('class', 'form-title--title');
                form.appendChild(msgs);
                msgs.setAttribute('class', 'msgs');

                form.appendChild(inpName);
                form.appendChild(inpTell);
                form.appendChild(formInfo);
                form.appendChild(formReferer);
                form.appendChild(formBtn);
                form.appendChild(a);
                div.appendChild(copyright);
                copyright.appendChild(copyrightLink);

                a.setAttribute('class', 'sl-overlay--close');
                a.setAttribute('href', '#modal-close');
                a.innerHTML = '×';

                div.setAttribute('class', this.style);
                form.setAttribute('method', 'POST');
                form.setAttribute('id', 'firstForm');
                form.setAttribute('class', 'sl-form');
                form.setAttribute('autocomplete', 'off');
                formTitleIcon.setAttribute('class', 'form-title--icon');
                formTitleIcon.innerHTML = callFormImg;
                formTitleText.setAttribute('class', 'form-title--text');
                inpName.setAttribute('type', 'text');
                inpName.setAttribute('name', 'uname');
                inpName.setAttribute('class', 'sl-form--input');
                inpName.setAttribute('placeholder', placeHolderName);
                if(requiredName) {
                  inpName.setAttribute('required', 'required');
                }
                inpTell.setAttribute('type', 'tel');
                inpTell.setAttribute('name', 'uphone');
                inpTell.setAttribute('placeholder', placeHolderTel);
                inpTell.setAttribute('class', 'sl-form--input');
                if(requiredTel) {
                  inpTell.setAttribute('required', 'required');
                }
                inpTell.setAttribute('pattern', '^[ 0-9]+$');
                formInfo.setAttribute('type', 'hidden');
                formInfo.setAttribute('name', 'formInfo');
                formReferer.setAttribute('type', 'hidden');
                formReferer.setAttribute('name', 'formReferer');
                formBtn.setAttribute('class', 'sl-form--button');
                formBtn.setAttribute('title', 'Заказали обратный звонок');
                formBtn.setAttribute('type', 'submit');
                copyright.setAttribute('class', 'copyright-wprapper');
                copyrightLink.setAttribute('class', 'copyright-link');
                copyrightLink.setAttribute('href', 'http://smartlanding.biz/smartlid');
                copyrightLink.innerHTML = "smartLid";
                formBtn.innerHTML = 'Отправить';

                view.displayCallForm(div);
                controller.setFormInfo();
                controller.setFormReferer();
                controller.sendForm();
                return div;
            };
        }
    };
    model.createButton();
  });