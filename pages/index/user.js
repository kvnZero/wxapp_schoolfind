
const app = getApp()

Page({
    data: {
        userInfo: {},
        hasUserInfo: false,
        canIUse: wx.canIUse('button.open-type.getUserInfo'),
        countpush:0,
        countemail:0
    },
    onLoad: function () {
        if (app.globalData.userInfo) {
            this.setData({
                userInfo: app.globalData.userInfo,
                hasUserInfo: true
            })
        } else if (this.data.canIUse) {
            app.userInfoReadyCallback = res => {
                this.setData({
                    userInfo: res.userInfo,
                    hasUserInfo: true
                })
            }
        } else {
            wx.getUserInfo({
                success: res => {
                    app.globalData.userInfo = res.userInfo
                    this.setData({
                        userInfo: res.userInfo,
                        hasUserInfo: true
                    })
                }
            })
        }
        if (app.globalData.userInfo){
            updateuser(app.globalData.userInfo, app.globalData.openid)
        }
    },
    getUserInfo: function (e) {
        app.globalData.userInfo = e.detail.userInfo
        this.setData({
            userInfo: e.detail.userInfo,
            hasUserInfo: true
        })
    },onShow(){
        var that = this;
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getcount&wx_openid=' + app.globalData.openid,
            success(res) {
                that.setData({
                    countpush: res.data.cpush,
                    countemail: res.data.cemail,
                })
            }
        })
    },
})

function updateuser(user,openid){
    var nickname = user.nickName;
    var avatarurl = user.avatarUrl;
    wx.request({
        url: 'https://sapp.itcspark.com/api.php?type=update',
        data: {
            wx_openid: openid,
            wx_name: nickname,
            wx_aurl: avatarurl
        },
        header: {
            'content-type': 'application/x-www-form-urlencoded'
        },
        method: 'POST',
        success(res) {
            console.log(res.data);
        }
    })
    
}