<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="options"
      :server-items-length="total"
      :loading="loading"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-btn color="indigo" @click="createItem" depressed dark>Add</v-btn>
          <v-spacer></v-spacer>
          <v-spacer></v-spacer>
          <v-text-field v-model="search" v-debounce="runSearch" label="Search" class="mt-7" outlined dense></v-text-field>
        </v-toolbar>
      </template>
      <template slot="item" slot-scope="props">
        <tr>
          <td v-for="header in headers" :key="header['value']">
            <div v-if="!header['value'].endsWith('_icon')" class="pointer" @click="performAction(props.item)">{{props.item[header['value']]}}</div>
            <div v-if="header['value'].endsWith('_icon')">
              <v-icon>{{props.item[header['value']]}}</v-icon>
            </div>
            <div v-if="header['value'] === 'action'" class="float-right">
              <v-btn v-if="smartActions.indexOf('edit') >= 0" icon class="mx-0" @click="editItem(props.item)">
                <v-icon>mdi-playlist-edit</v-icon>
              </v-btn>
              <v-btn v-if="smartActions.indexOf('delete') >= 0" icon class="mx-0" @click="deleteItem(props.item)">
                <v-icon>mdi-delete-sweep</v-icon>
              </v-btn>
            </div>
          </td>
        </tr>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  export default {
    props: ['uri', 'smartActions'],
    data() {
      return {
        items: [],
        headers: [],
        options: {
          page: 1,
          itemsPerPage: 10,
          sortBy: [],
          sortDesc: [],
          groupBy: [],
          groupDesc: [],
          mustSort: false,
          multiSort: false
        },
        total: 0,
        loading: true,
        search: null
      }
    },
    watch: {
      options: {
        handler () {
          if (this.loading)
            return
          
          this.getDataFromApi()
        },
        deep: true,
      }
    },
    methods: {
      appendUrl(url, param, value) {
        if (url.indexOf(param) === -1) {
          let sep = url.indexOf("?") === -1 ? "?" : "&";
          return url + sep + param + "=" + value;
        }
        return url;
      },
      async getDataFromApi () {
        this.loading = true
        let data = await this.fetchData()
        
        return new Promise((resolve) => {
          const { sortBy, sortDesc, page, itemsPerPage } = this.options
          let items = data.data,
            headers = data.headers
          
          const total = data.meta.total
    
          if (sortBy.length === 1 && sortDesc.length === 1) {
            items = items.sort((a, b) => {
              const sortA = a[sortBy[0]]
              const sortB = b[sortBy[0]]
        
              if (sortDesc[0]) {
                if (sortA < sortB) return 1
                if (sortA > sortB) return -1
                return 0
              } else {
                if (sortA < sortB) return -1
                if (sortA > sortB) return 1
                return 0
              }
            })
          }
    
          if (itemsPerPage > 0) {
            items = items.slice((page - 1) * itemsPerPage, page * itemsPerPage)
          }
    
          setTimeout(() => {
            this.loading = false
            resolve({
              items,
              headers,
              total
            })
          }, 1000)
        })
      },
      async fetchData() {
        const { sortBy, sortDesc, page, itemsPerPage } = this.options
        let mod_uri = this.uri
  
        mod_uri = this.appendUrl(mod_uri, 'pagination_count', itemsPerPage)
        mod_uri = this.appendUrl(mod_uri, 'page', page)
        
        if (this.search != null)
          mod_uri = this.appendUrl(mod_uri, 'search', this.search)
        
        if (sortBy != null)
          mod_uri = this.appendUrl(mod_uri, 'search', sortBy)
  
        let direction = sortDesc && sortDesc[0] ? 'desc' : 'asc'
        mod_uri = this.appendUrl(mod_uri, 'sortDir', direction)
        
        let data = await this.$http.get(mod_uri)
        return data.data
      },
      performAction(item) {
        this.$emit('smart-table-row-clicked', item)
      },
      createItem() {
        this.$emit('smart-table-add')
      },
      editItem(item) {
        this.$emit('smart-table-edit', item)
      },
      deleteItem(item) {
        this.$emit('smart-table-delete', item)
      },
      runSearch() {
        this.options.page = 1;
        this.getDataFromApi().then(data => {
          this.items = data.items
          this.headers = data.headers
          this.total = data.total
        })
      }
    },
    mounted() {
      this.getDataFromApi().then(data => {
        this.items = data.items
        this.headers = data.headers
        this.total = data.total
      })
    }
  }
</script>

<style scoped>

</style>