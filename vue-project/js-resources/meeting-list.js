const axiosIns = axios.create({
    baseURL: window.baseURL,
    timeout: 1000
})

const vm = new Vue({
    el: "#app",

    data: {
        tabKey: "m",
        tabs: [{ key: "m", text: "本月台账" }, { key: "y", text: "本年台账" }, { key: "r", text: "笔记排名" }],
        keyword: "",

        mList: [], //月台账
        yList: [], //年台账
        rList: [], //排名

        mPage: 1,
        yPage: 1,
        rPage: 1,

        isLoading: false,

        scrollTimer: null,

        isEditable: false,
        showAddLayer: false
    },

    computed: {
        showingList() {
            return this[this.tabKey + "List"]
        }
    },

    methods: {
        getList() {
            var url = "/Meeting/index"
            axiosIns.get(url, { params: { keyword: this.keyword, type: this.tabKey, p: 1 } }).then(res => {
                if (res.data.code === 200) {
                    console.log("list", res)
                    this[this.tabKey + "List"] = resultFormate(res.data.data.meetinglist)
                } else if (res.data.code === 404) {
                    this[this.tabKey + "List"] = []
                }
                this.isEditable = res.data.data.allowcreate === 1
            })
        },

        getMore() {
            if (this.isLoading) {
                return
            }

            this.isLoading = true

            var url = "/Meeting/index"
            axiosIns
                .get(url, { params: { keyword: this.keyword, type: this.tabKey, p: this[this.tabKey + "Page"] + 1 } })
                .then(res => {
                    if (res.data.code === 200) {
                        this[this.tabKey + "List"] = this[this.tabKey + "List"].concat(resultFormate(res.data.data.meetinglist))
                        this[this.tabKey + "Page"]++
                    }
                })
                .finally(() => {
                    this.isLoading = false
                })
        },

        onTab(key) {
            this.tabKey = key

            if (this[key + "List"].length === 0) {
                this.getList()
            }
        },

        onScroll(e) {
            var offsetHeight = e.target.offsetHeight
            var contentHeight = e.target.scrollHeight
            var maxScrollTop = contentHeight - offsetHeight
            var scrollTop = e.target.scrollTop

            if (maxScrollTop - scrollTop < 100) {
                var self = this
                self.scrollTimer && clearTimeout(self.scrollTimer)
                self.scrollTimer = setTimeout(function() {
                    self.getMore()
                }, 500)
            }
        },

        // 点击收藏或赞
        onLikeOrCollection(id, type) {
            const item = this[`${this.tabKey}List`].find(v => v.id === id)
            if (!item) {
                return
            }

            const url = type === "like" ? "Meeting/likeAction" : "Meeting/collectionAction"
            const form = new FormData()
            const formKey = `is_${type}`
            const statusKey = type === "like" ? "isLike" : "isCollection"
            form.append("id", id)
            form.append(formKey, !item[statusKey] ? "1" : "0")

            axiosIns.post(url, form, { headers: { "Content-Type": "multipart/form-data" } }).then(res => {
                if (res.data.code === 200) {
                    item[statusKey] = !item[statusKey]
                    item[type] += item[statusKey] ? 1 : -1
                }
            })
        },

        onAddToggle(boolean) {
            this.showAddLayer = boolean
        },

        onNavToDetail(id) {
            window.location.href = "../MeetingDetail/index.html?id=" + id
        },

        goback() {
            window.history.back()
        }
    },

    mounted() {
        this.getList()
    }
})

// 将后台的数据转化为前端需要的数据
function resultFormate(list) {
    if (!list || list.length === 0) {
        return
    }

    return list.map(function(item) {
        var classes = ["type one", "type two", "type three", "type four"]
        return {
            id: item.id,
            date: item.create_at,
            type: item.meeting_type,
            typeClass: classes[item.meeting_type_id],
            img: "url('../../static/images/meeting-default-image.png')",
            avatar: "url(" + item.img + ")",
            title: item.theme,
            author: item.realname,
            branch: item.branch_name,
            read: item.pageviews,
            collection: item.collection,
            comment: item.comment,
            like: item.likenum,
            isCollection: item.is_collection === "1",
            isLike: item.is_like === "1",
            likeId: item.like_id, // 用于更新点赞
            collectionId: item.collection_id // 用于更新收藏
        }
    })
}
