
const app = getApp()
Page({
    data: {
        select: "index",
        imgUrls: [],
        hotList: [],
        page:1
    },
    onLoad: function () { 

        var that = this;
        wx.showToast({ title: '更新中', icon: 'loading' });
        wx.showNavigationBarLoading()
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getinfo',
            success: function (res) {
                if (res.data.items.length != 0) {
                    that.setData({
                        hotList: res.data.items,
                        page :1
                    })
                } else {
                    wx.showToast("已经加载全部")
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
            },
            complete: function () {
                wx.hideNavigationBarLoading()
            }

        })
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getadb',
            success: function (res) {
                that.setData({
                    imgUrls:res.data,
                })

            },
            fail: function () {
                wx.showToast({ title: '滚动图加载失败', icon: 'none' });
            },
        })

    },
    onPullDownRefresh() {
        var that = this;
        wx.showToast({title: '更新中', icon: 'loading'});
        wx.showNavigationBarLoading() 
　　    wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getinfo',
            success: function (res) {
                if (res.data.items.length!=0){
                    that.setData({
                        hotList: res.data.items,
                        page: 1
                    })
                }else{
                    wx.showToast("已经加载全部")
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
            },
            complete: function () {
                wx.hideToast();
                wx.hideNavigationBarLoading()
                wx.stopPullDownRefresh()
            }
        })
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getadb',
            success: function (res) {
                that.setData({
                    imgUrls: res.data,
                })
            },
            fail: function () {
                wx.showToast({ title: '滚动图加载失败', icon: 'none' });
            },
        })
    },
    onReachBottom: function () {
        var that = this;
        var page = that.data.page + 1
        wx.showToast({ title: '更新中', icon: 'loading' });
        wx.showNavigationBarLoading()
        wx.request({
            url: 'https://sapp.itcspark.com/api.php?type=getinfo&page='+page,
            success: function (res) {
                console.log('https://sapp.itcspark.com/api.php?type=getinfo&page='+page)
                var itemList = [];
                if(res.data.items.length==0){
                    wx.showToast({
                        title: '已经是最底了',
                        icon: 'none'
                    })
                }else{
                    for (var i = 0; i < res.data.items.length; i++) {
                        itemList.push(res.data.items[i]);
                    }
                    var newList = that.data.hotList.concat(itemList)
                    that.setData({
                        hotList: newList,
                        page: page
                    })
                }
               
            },
            fail: function () {
                wx.showModal({
                    title: '提示',
                    content: '加载失败,请检查网络状态!',
                    showCancel: false,
                    success: function (res) {
                        wx.navigateBack({
                            delta: 1
                        })
                    }
                })
            },
            complete: function () {
                wx.hideNavigationBarLoading()
                wx.stopPullDownRefresh()
            }
        })
    },
})
