import React from 'react'
import {Navbar, Nav, NavItem, FormGroup, FormControl, Button} from 'react-bootstrap'
import {withRouter} from 'react-router-dom'
import {Locs} from '../l10n'


class Header1 extends React.Component {
    constructor(props) {
        super(props);
        this.locs = Locs();
        this.state = {selectedIndex: 0}

    }

    select = (index, event) => {
        this.setState({
            selectedIndex: index,
        });
        let pg = this.locs.home
        let route = '/'
        switch (index) {
            case 0:
                pg = this.locs.home;
                route = '/';
                break;
            case 1:
                pg = this.locs.titles;
                route = '/titles';
                break;
            case 2:
                pg = this.locs.authors;
                route = '/authors';
                break;
            case 3:
                pg = this.locs.tags;
                route = '/tags';
                break;
            case 4:
                pg = this.locs.series;
                route = '/series';
                break;
            default:
                pg = this.locs.home;
                route = '/';
                break;

        }
        this.props.history.push(route)
    }

    render() {
        return (
            <Navbar collapseOnSelect={true} fixedTop fluid={true}>
                <Navbar.Header>
                    <Navbar.Brand>
                        <a>BicBucStriim</a>
                    </Navbar.Brand>
                    <Navbar.Toggle/>
                </Navbar.Header>

                <Navbar.Collapse>
                    <Navbar.Form pullLeft>
                        <FormGroup>
                            <FormControl type="text" placeholder={this.locs.pagination_search_ph}/>
                        </FormGroup>
                        {' '}
                        <Button type="submit">{this.locs.pagination_search_lbl}</Button>
                    </Navbar.Form>
                    <Nav activeKey={this.state.selectedIndex} onSelect={this.select} pullRight>
                        <NavItem eventKey={0}>{this.locs.home}</NavItem>
                        <NavItem eventKey={1} href="#">{this.locs.titles}</NavItem>
                        <NavItem eventKey={2} href="#">{this.locs.authors}</NavItem>
                        <NavItem eventKey={3} href="#">{this.locs.tags}</NavItem>
                        <NavItem eventKey={4} href="#">{this.locs.series}</NavItem>
                    </Nav>
                </Navbar.Collapse>
            </Navbar>
        )
    }
}

const Header = withRouter(Header1)
export default Header

