// pages/index/write.js
const app = getApp();
Page({
    data: {
        images: []
    },
    formSubmit(e) {
        var that = this;
        wx.getSetting({
            success: res => {
                if (!res.authSetting['scope.userInfo']) {
                    wx.showToast({
                        title: '请先授权用户登录:点击右下角我的 - 一键登录',
                        icon: 'none'
                    })
                    return 0;
                }
            }
        })

        var type = e.detail.value.type;
        var address = e.detail.value.address;
        if (address=="") {
            wx.showToast({
                title: '需要填写丢失或捡到地址',
                icon: 'none'
            })
            return 0;
        }
        var phone = e.detail.value.phone;
        var text = e.detail.value.text;
        var cardid = e.detail.value.cardid;
        if (text == "") {
            wx.showToast({
                title: '需要填写物品的相关内容',
                icon:'none'
            })
            return 0;
        }
        var openid = app.globalData.openid;
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=push',
            data: {
                wx_openid: openid,
                c_type: type,
                c_text: text,
                c_address:address,
                c_phone : phone,
                c_cardid: cardid,
            },
            header: {
                'content-type': 'application/x-www-form-urlencoded'
            },
            method: 'POST',
            success(res) {
                if(res.data.return=="10000"){
                    wx.showToast({
                        title: '发布成功',
                    })
                    wx.reLaunch({
                        url: '/pages/index/index'
                    })
                }else{
                    wx.showToast({
                        title: '发布失败',
                        icon: 'none'
                    })
                }
            }
        })
    },
})