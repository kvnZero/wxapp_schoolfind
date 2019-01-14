// pages/index/card.js
const app = getApp();
Page({
    data: {
        cardid:""
    },
    onLoad: function (options) {
        var that = this;
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getcardid&wx_openid=' + app.globalData.openid,
            success(res) {
                that.setData({
                    cardid: res.data.cardid,
                })
            }
        })
    },
    formSubmit(e) {
        var that = this;
        var cardid = e.detail.value.cardid;
        if (cardid == "") {
            wx.showToast({
                title: '需要填写6位卡号',
                icon: 'none'
            })
            return 0;
        }
        var openid = app.globalData.openid;
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=ccard',
            data: {
                wx_openid: openid,
                card_id: cardid
            },
            header: {
                'content-type': 'application/x-www-form-urlencoded'
            },
            method: 'POST',
            success(res) {
                console.log(res.data)
                wx.showToast({
                    title: res.data.return,
                })
            }
        })
    },
})