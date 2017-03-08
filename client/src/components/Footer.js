import React from 'react';
import {Navbar, Nav, NavItem,  FormGroup, FormControl, Button} from 'react-bootstrap'

import {Locs} from '../l10n'


class Footer extends React.Component {
	constructor() {
	    super();
	    this.locs = Locs();
	    this.state = { selectedIndex: 0 }
  	}	

  	select = (index,event) => {
  		this.setState({
  			selectedIndex: index,
  			});
  		let pg = this.locs.home
  		let route = '/'
  		switch(index) {
  			case 100: route = '/admin'; break;
  			case 101: route = '/logout'; break;
  			default: route = '/'; break;

  		}
  		this.context.history.push(route)
  	}

	render() {
		return (
			<Navbar collapseOnSelect={true} fixedBottom>
    			<Navbar.Text pullLeft>
            Copyright (C) 2012-{(new Date()).getFullYear()} Rainer Volz
    			</Navbar.Text>

          <Navbar.Collapse>
            <Nav pullRight>
              <NavItem eventKey={100} href="#">{this.locs.admin_short}</NavItem>
              <NavItem eventKey={101} href="#">{this.locs.logout}</NavItem>
            </Nav>  
          </Navbar.Collapse>
			 </Navbar>
		)
	}
}

Footer.contextTypes = {
  history: React.PropTypes.shape({
    push: React.PropTypes.func.isRequired
  })
}

export default Footer;
