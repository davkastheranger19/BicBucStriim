import React, { Component } from 'react'
import {BrowserRouter as Router, Route, Switch} from 'react-router-dom'


import './App.css'
import Home from './components/Home'
import Titles from './components/Titles'
import Authors from './components/Authors'
import Tags from './components/Tags'
import Series from './components/Series'
import TitleDetail from './components/TitleDetail'
import NotFound from './components/NotFound'
import Navbar2 from './components/Navbar2'

import {Locs} from './l10n'

class App extends Component {

  constructor() {
    super()
    this.setPageTitle = this.setPageTitle.bind(this)
    this.locs = Locs()
    this.state = {
      pagetitle: this.locs.home,
    }
  }

  setPageTitle(title) {
    this.setState({pagetitle: title})
  }

  render() {
    return (
      <Router>
        <div>
          <Navbar2 setPageTitle={this.setPageTitle}/>
          <Switch>         
            <Route exact={true} path="/" component={Home}/>
            <Route path="/titles/:id" component={TitleDetail}/>
            <Route path="/titles" component={Titles}/>
            <Route path="/authors" component={Authors}/>
            <Route path="/tags" component={Tags}/>
            <Route path="/series" component={Series}/>
            <Route component={NotFound}/>
          </Switch>
        </div>
      </Router>
    )
  }
}

export default App
