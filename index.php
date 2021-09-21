<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <title>短信获取京东COOKIE</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/login.css?v=1.072" />
    <link rel="stylesheet" type="text/css" href="./assets/css/loading.css" />
    <link rel="stylesheet" type="text/css" href="./assets/iview.css">
    <script type="text/javascript" src="./assets/vue.min.js"></script>
    <script type="text/javascript" src="./assets/iview.js"></script>
    <script src="./assets/axios.min.js"></script>
</head>

<body>
    <Spin fix id="loading" v-if="show">
        <Icon type="ios-loading" size=60 class="demo-spin-icon-load"></Icon>
        <br>
        <div class="loading-text">加载中...</div>
    </Spin>
    <script>
        var loading = new Vue({
            el: "#loading",
            data: {
                show: true
            },
            created: function() {
                setTimeout("loading.show=false", 500);
            }
        });
    </script>
    <div id="app">
        <i-row gutter="24" class="login-page ">
            <tabs class="my-tabs">
                <tab-pane label="短信登录" icon="md-mail">
                    <i-col :xs="24" class="login-form">
                        <p class="title">短信获取COOKIE</p>
                        <p class="description">请输入注册的手机号获取</p>
                        <i-input prefix="md-contact" v-model="mobile" size="large" placeholder="手机号"></i-input>
                        <div style="display: flex;width: 100%;justify-content: space-between;">
                            <i-input prefix="ios-keypad-outline" v-model="smsCode" size="large" placeholder="验证码" style="flex-grow: 2;margin-top: 10px;"></i-input>
                            <i-button :loading="r_isLoading" id="sendCode" @click="sendsmsCode" size="large" :class="{'disabled-style':sendMsgDisabled}" :disabled="sendMsgDisabled">	<span class="sendMsg" v-if="!sendMsgDisabled">获取验证码</span>
                                <span class="sendMsg" v-if="sendMsgDisabled">{{codeTime+'秒后获取'}}</span>
                            </i-button>
                        </div>
                        <div style="display: flex;width: 100%;justify-content: space-between;">
                            <i-input v-model="ps" size="large" placeholder="备注信息" style="flex-grow: 1;margin-top: 10px;margin-right: 5px;" @on-blur="getps">{{ps}}</i-input>
                        </div>
                        <i-button :loading="isLoading" @click="getCK" size="large" type="primary" icon="md-finger-print" style="width: 100%; margin-top: 10px;">获取CK</i-button>
                        <div class="my-qr">
                            <Poptip trigger="hover" title="扫一扫，添 加 微 信">
                                <a href="javascript:void(0)">
                                    <img src="./images/wx.svg" class="footer-social-icon">
                                </a>
                                <div slot="content">
                                    <img :src="wx_img" class="footer-qr">
                                </div>
                            </Poptip>
                            <Poptip trigger="hover" title="扫一扫，加入 QQ 群">
                                <a href="javascript:void(0)">
                                    <img src="./images/qq.svg" class="footer-social-icon">
                                </a>
                                <div slot="content">
                                    <img :src="qq_img" class="footer-qr">
                                </div>
                            </Poptip>
                        </div>
                    </i-col>
                </tab-pane>
                <tab-pane label="ZACK 工具" icon="md-settings">
                    <i-col :xs="24" class="login-form">
                        <p class="title">wskey转CK</p>
                        <i-input v-model="pin" size="large" placeholder="输入pin"><span slot="prepend">pin</span>
                        </i-input>
                        <i-input v-model="wskey" size="large" placeholder="输入wskey" style="margin-top: 10px;"><span slot="prepend">wskey</span>
                        </i-input>
                        <i-button :loading="isLoading" @click="go_wskey" size="large" type="primary" icon="md-finger-print" style="width: 100%; margin-top: 10px;">转换</i-button>
                        <collapse simple v-model="value1" class="more-tools">
                            <panel name="1">更多工具
                                <p slot="content" style="text-align: center;">
                                    <button-group>
                                        <i-button :loading="isLoading" @click="codekey" size="large" icon="md-git-commit" style="margin-top: 10px;" long>口令转链接</i-button>
                                        <i-button :loading="isLoading" @click="keycode" size="large" icon="md-lock" style="margin-top: 10px;" long>转京口令</i-button>
                                        <i-button :loading="isLoading" @click="ShortUrl" size="large" icon="md-link" style="margin-top: 10px;" long>转短链接</i-button>
                                    </button-group>
                                </p>
                            </panel>
                        </collapse>
                    </i-col>
                </tab-pane>
            </tabs>
        </i-row>
        <div id="footer"></div>
    </div>
    <script type="text/javascript" src="./assets/sms.js?v0.01"></script>
</body>

</html>