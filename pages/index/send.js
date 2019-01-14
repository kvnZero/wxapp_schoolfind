// pages/index/send.js
const app = getApp()
Page({
    data: {
        sendto:"",
        username:""
    },
    onShow: function (options) {
        this.data.sendto =  getsendto()
        this.data.username = getsendto(true)
        this.setData({
            username: this.data.username
        })
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
        var text = e.detail.value.text;
        if (text == "") {
            wx.showToast({
                title: '请填写邮件内容',
                icon: 'none'
            })
            return 0;
        }
        var openid = app.globalData.openid;
        var sendto = that.data.sendto;
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=send',
            data: {
                wx_openid: openid,
                s_text: text,
                s_sendto: sendto,
            },
            header: {
                'content-type': 'application/x-www-form-urlencoded'
            },
            method: 'POST',
            success(res) {
                console.log(res.data)
                if (res.data.return == "10000") {
                    wx.showToast({
                        title: '发送成功',
                    })

                    setTimeout(function () {
                        wx.navigateBack({
                            delta: 1
                        })
                    }, 1500)
                } else {
                    wx.showToast({
                        title: '发送失败,请检查网络状态',
                        icon: 'none'
                    })
                }
            },

        })
    }
})
function getsendto(name=false) {
    var pages = getCurrentPages() 
    var currentPage = pages[pages.length - 1] 
    var url = currentPage.route 
    var options = currentPage.options 
    if(name){
        console.log(options);
        return options.name;
    }else{
        return options.sendto;
    }
}