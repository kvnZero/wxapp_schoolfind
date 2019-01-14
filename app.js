//app.js
App({
  onLaunch: function () {
    wx.login({
      success: res => {
          if (res.code) {
              wx.request({
                  url: 'https://sapp.itcspark.com/api.php?type=login&lg_code=' + res.code,
                  success: function (res) {
                      if (res.data.openid) {
                          var app = getApp();
                          app.globalData.openid = res.data.openid;
                          console.log("已经登录:" + res.data.openid);
                      } else {
                          wx.showModal({
                              title: '提示',
                              content: '登录失败,请检查网络状态？',
                              showCancel: false,
                              success: function (res) {
                                  wx.navigateBack({
                                      delta: 1
                                  })
                              }
                          })
                      }
                  },
                  fail: function () {
                      wx.showModal({
                          title: '提示',
                          content: '加载失败,请检查网络状态？',
                          showCancel: false,
                          success: function (res) {
                              wx.navigateBack({
                                  delta: 1
                              })
                          }
                      })
                  }
              })
          } else {
              console.log('登录失败！' + res.errMsg)
          }
          
          
      }
    })
    // 获取用户信息
    wx.getSetting({
      success: res => {
        if (res.authSetting['scope.userInfo']) {
          wx.getUserInfo({
            success: res => {
              this.globalData.userInfo = res.userInfo
              if (this.userInfoReadyCallback) {
                this.userInfoReadyCallback(res)
              }
            }
          })
        }else{
            wx.reLaunch({
                url: "/pages/index/user"
            })
        }
      }
    })
  },
  globalData: {
    userInfo: null,
    openid:null
  }
})