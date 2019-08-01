/**
 * @name ImgUploader
 * @desc 基于 webUploader 的图片上传插件
 * @author darcrand
 * @version 2018-09-05
 */

;(function($, WebUploader) {
    function ImgUploader(options) {
      this.options = {
        accept: {
          title: 'Images',
          extensions: 'gif,jpg,jpeg,bmp,png',
          mimeTypes: 'image/*'
        },
        fetchedImgs: [],
        selectorList: '#list',
        selectorAdd: '#select',
        selectorAddLabel: '#select-label',
        selectorImgItem: '.img-item',
        selectorRemove: '.remove-btn',
        selectorUpload: '#upload',
        removeText: '删除',
        server: '',
        onFinished: null
      }
      this.instance = null
      this.files = []
      this.$add = null
      this.addLabel = null
      this.$list = null
      this.$upload = null
  
      this.init(options)
    }
  
    ImgUploader.prototype.init = function(options) {
      this.options = Object.assign({}, this.options, options)
      this.instance = WebUploader.create(this.options)
      this.$list = $(this.options.selectorList)
      this.$add = $(this.options.selectorAdd)
      this.$addLabel = $(this.options.selectorAddLabel)
      this.$upload = $(this.options.selectorUpload)
  
      this.createDefaultImgs()
      this.bindAdd()
      this.bindRemove()
      this.bindUpload()
      this.bindUploadEvents()
    }
  
    /**
     * @desc 初始化的时候,需要获取该记录原来存在的图片,把它们创建出来
     */
    ImgUploader.prototype.createDefaultImgs = function() {
      var that = this
      if (!that.options.fetchedImgs.length) {
        return
      }
      that.options.fetchedImgs.forEach(function(item) {
        createPreviewImage(item, function(file, src) {
          var $imgTemplate = $(
            '<div class="' +
              that.options.selectorImgItem.slice(1) +
              '" style="background-image:url(\'' +
              src +
              '\')"><span class="' +
              that.options.selectorRemove.slice(1) +
              '">' +
              that.options.removeText +
              '</span></div>'
          )
  
          that.$addLabel.before($imgTemplate)
          that.files.push(file)
        })
      })
    }
  
    /**
     * @desc 切换月份的时候,重新渲染
     * @param {Array} imgs 新的默认图片
     */
    ImgUploader.prototype.reset = function(imgs) {
      this.files = []
      this.instance.reset()
      this.$list.find(this.options.selectorImgItem).remove()
      this.options.fetchedImgs = imgs || []
      this.createDefaultImgs()
    }
  
    /**
     * @desc 绑定选择图片时,input元素的files值的变化.一旦有新的文件选中,则创建预览
     */
    ImgUploader.prototype.bindAdd = function() {
      var that = this
  
      that.$add.on('change', function(e) {
        var files = e.target.files
        if (files && files.length) {
          for (var i = 0; i < files.length; i++) {
            createPreviewImage(files[i], function(file, src) {
              var $imgTemplate = $(
                '<div class="' +
                  that.options.selectorImgItem.slice(1) +
                  '" style="background-image:url(\'' +
                  src +
                  '\')"><span class="' +
                  that.options.selectorRemove.slice(1) +
                  '">' +
                  that.options.removeText +
                  '</span></div>'
              )
  
              that.$addLabel.before($imgTemplate)
              that.files.push(file)
            })
          }
        }
      })
    }
  
    /**
     * @desc 绑定图片的删除
     */
    ImgUploader.prototype.bindRemove = function() {
      var that = this
      var selectorRemove = this.options.selectorRemove
      var selectorImgItem = this.options.selectorImgItem
  
      that.$list.on('click', selectorRemove, function() {
        var index = $(selectorRemove).index($(this))
        if (index !== -1) {
          that.$list
            .find(selectorImgItem)
            .eq(index)
            .remove()
  
          that.files.splice(index, 1)
        }
      })
    }
  
    /**
     * @desc 上传图片
     */
    ImgUploader.prototype.bindUpload = function() {
      var that = this
  
      that.$upload.on('click', function() {
        if (that.files.length) {
          that.instance.reset()
          that.instance.addFiles(that.files)
          that.instance.upload()
        }
      })
    }
  
    /**
     * @desc WebUploader 内部事件监听
     */
    ImgUploader.prototype.bindUploadEvents = function() {
      var that = this
      this.instance.on('uploadFinished', function() {
        if (that.options.onFinished) {
          that.options.onFinished()
        }
      })
    }
  
    /**
     * @desc 创建预览图片的dom元素
     * @param {Object} file 文件对象
     * @param {Function} callback 回调函数
     * @param {Object} callback.file 原来的或生成的文件对象
     * @param {String} callback.src 图片文件的src
     */
    function createPreviewImage(file, callback) {
      if (!file.url) {
        var reader = new FileReader()
        reader.onload = function(e) {
          if (callback) {
            callback(file, e.target.result)
          }
        }
        reader.readAsDataURL(file)
      } else {
        if (callback) {
          var imgOnlyUrl = new File(['image'], 'imgId_' + file.id, { type: 'url' })
          callback(imgOnlyUrl, file.url)
        }
      }
    }
  
    window.ImgUploader = ImgUploader
  })(window.jQuery, window.WebUploader)
  