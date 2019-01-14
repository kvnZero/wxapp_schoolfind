const app = getApp();
Page({

    data: {
        hotList: [],
        touchStartTime: 0,
        touchEndTime: 0
    },
    onShow: function () {
        var that = this;
        wx.showToast({ title: '更新中', icon: 'loading' });
        wx.showNavigationBarLoading()
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getmyemail&wx_openid=' + app.globalData.openid,
            header: {
                "Content-Type": "application/json"
            },
            method: 'GET',
            success: function (res) {

                if (res.data.items.length != 0) {
                    
                    that.setData({
                        hotList: res.data.items,
                    })
                } else {
                    wx.showToast("已经加载全部")
                }
                wx.hideToast();
            },
            fail: function () {
                wx.showToast("加载失败,请检查网络状态")
            },
            complete: function () {
                wx.hideToast();
                wx.hideNavigationBarLoading()
                wx.stopPullDownRefresh()
            }
        })
    }, touchStart: function (e) {
        this.touchStartTime = e.timeStamp
    },

    /// 按钮触摸结束触发的事件
    touchEnd: function (e) {
        this.touchEndTime = e.timeStamp
    },
    doubleTap: function (e) {
        var that = this
        if (that.touchEndTime - that.touchStartTime < 350) {
            var currentTime = e.timeStamp
            var lastTapTime = that.lastTapTime
            that.lastTapTime = currentTime
            if (currentTime - lastTapTime < 300) {
                clearTimeout(that.lastTapTimeoutFunc);
                wx.showModal({
                    title: '提示',
                    content: '确定要删除此条信息？',
                    success: function (res) {
                        if (res.confirm) {
                            wx.request({
                                url: 'https://sapp.itcspark.com/api.php?type=deleteemail&cid=' + e.currentTarget.dataset.id,
                                success: function (res) {
                                    if (res.data.return == "10000") {
                                        wx.showToast({
                                            title: '删除成功'
                                        })
                                        wx.request({
                                            url: 'https://sapp.itcspark.com/api.php?type=getmyemail&wx_openid=' + app.globalData.openid,
                                            success: function (res) {
                                            that.setData({
                                                hotList: res.data.items,
                                            })
                                            }
                                        })
                                    } else {
                                        wx.showToast({
                                            title: '发布失败',
                                            icon: 'none'
                                        })
                                    }
                                },
                                fail: function () {                                                          wx.showToast({
                                        title: '删除失败,请检查网络状态',
                                        icon: 'null'
                                    })
                                },
                            })
                        } else if (res.cancel) {
                            return false;
                        }
                    }
                })
            }
        }
    },
    longpress: function (e) {
        var uid = 0;
        var name = "";
        var fromwx = e.currentTarget.dataset.fromid;
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getuser&wx_openid=' + fromwx,
            success: function (res) {
                console.log(res.data);
                if (res.data.uid) {
                    wx.navigateTo({
                        url: "send?sendto=" + res.data.uid + "&name=" + res.data.name
                    })
                } else {
                    wx.showToast({
                        title: '获取发信用户失败',
                        icon: 'null'
                    })
                }
            },
            fail: function () {
                wx.showToast({
                    title: '获取发信用户失败',
                    icon: 'null'
                })
            },
        })

    }
})
