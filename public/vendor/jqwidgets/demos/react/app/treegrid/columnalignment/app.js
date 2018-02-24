import React from 'react';
import ReactDOM from 'react-dom';

import JqxTreeGrid from '../../../jqwidgets-react/react_jqxtreegrid.js';
import JqxDropDownList from '../../../jqwidgets-react/react_jqxdropdownlist.js';

class App extends React.Component {
    componentDidMount() {        
        this.refs.columnAlignment.on('change', (event) => {
            let index = event.args.index;
            switch (index) {
                case 0:
                    this.refs.myTreeGrid.setColumnProperty('FirstName', 'align', 'left');
                    this.refs.myTreeGrid.setColumnProperty('LastName', 'align', 'left');
                    this.refs.myTreeGrid.setColumnProperty('Title', 'align', 'left');
                    this.refs.myTreeGrid.setColumnProperty('BirthDate', 'align', 'left');
                    break;
                case 1:
                    this.refs.myTreeGrid.setColumnProperty('FirstName', 'align', 'center');
                    this.refs.myTreeGrid.setColumnProperty('LastName', 'align', 'center');
                    this.refs.myTreeGrid.setColumnProperty('Title', 'align', 'center');
                    this.refs.myTreeGrid.setColumnProperty('BirthDate', 'align', 'center');
                    break;
                case 2:
                    this.refs.myTreeGrid.setColumnProperty('FirstName', 'align', 'right');
                    this.refs.myTreeGrid.setColumnProperty('LastName', 'align', 'right');
                    this.refs.myTreeGrid.setColumnProperty('Title', 'align', 'right');
                    this.refs.myTreeGrid.setColumnProperty('BirthDate', 'align', 'right');
                    break;
            }
        });

        this.refs.cellsAlignment.on('change', (event) => {
            let index = event.args.index;
            switch (index) {
                case 0:
                    this.refs.myTreeGrid.setColumnProperty('FirstName', 'cellsAlign', 'left');
                    this.refs.myTreeGrid.setColumnProperty('LastName', 'cellsAlign', 'left');
                    this.refs.myTreeGrid.setColumnProperty('Title', 'cellsAlign', 'left');
                    this.refs.myTreeGrid.setColumnProperty('BirthDate', 'cellsAlign', 'left');
                    break;
                case 1:
                    this.refs.myTreeGrid.setColumnProperty('FirstName', 'cellsAlign', 'center');
                    this.refs.myTreeGrid.setColumnProperty('LastName', 'cellsAlign', 'center');
                    this.refs.myTreeGrid.setColumnProperty('Title', 'cellsAlign', 'center');
                    this.refs.myTreeGrid.setColumnProperty('BirthDate', 'cellsAlign', 'center');
                    break;
                case 2:
                    this.refs.myTreeGrid.setColumnProperty('FirstName', 'cellsAlign', 'right');
                    this.refs.myTreeGrid.setColumnProperty('LastName', 'cellsAlign', 'right');
                    this.refs.myTreeGrid.setColumnProperty('Title', 'cellsAlign', 'right');
                    this.refs.myTreeGrid.setColumnProperty('BirthDate', 'cellsAlign', 'right');
                    break;
            }
        });
    }
    render () {
        // prepare the data
        let employees = [
                   {
                       'EmployeeID': 2, 'FirstName': 'Andrew', 'LastName': 'Fuller', 'Country': 'USA', 'Title': 'Vice President, Sales', 'HireDate': '1992-08-14 00:00:00', 'BirthDate': '1952-02-19 00:00:00', 'City': 'Tacoma', 'Address': '908 W. Capital Way', 'expanded': 'true',
                       children: [
                           { 'EmployeeID': 8, 'FirstName': 'Laura', 'LastName': 'Callahan', 'Country': 'USA', 'Title': 'Inside Sales Coordinator', 'HireDate': '1994-03-05 00:00:00', 'BirthDate': '1958-01-09 00:00:00', 'City': 'Seattle', 'Address': '4726 - 11th Ave. N.E.' },
                           { 'EmployeeID': 1, 'FirstName': 'Nancy', 'LastName': 'Davolio', 'Country': 'USA', 'Title': 'Sales Representative', 'HireDate': '1992-05-01 00:00:00', 'BirthDate': '1948-12-08 00:00:00', 'City': 'Seattle', 'Address': '507 - 20th Ave. E.Apt. 2A' },
                           { 'EmployeeID': 3, 'FirstName': 'Janet', 'LastName': 'Leverling', 'Country': 'USA', 'Title': 'Sales Representative', 'HireDate': '1992-04-01 00:00:00', 'BirthDate': '1963-08-30 00:00:00', 'City': 'Kirkland', 'Address': '722 Moss Bay Blvd.' },
                           { 'EmployeeID': 4, 'FirstName': 'Margaret', 'LastName': 'Peacock', 'Country': 'USA', 'Title': 'Sales Representative', 'HireDate': '1993-05-03 00:00:00', 'BirthDate': '1937-09-19 00:00:00', 'City': 'Redmond', 'Address': '4110 Old Redmond Rd.' },
                           {
                               'EmployeeID': 5, 'FirstName': 'Steven', 'LastName': 'Buchanan', 'Country': 'UK', 'Title': 'Sales Manager', 'HireDate': '1993-10-17 00:00:00', 'BirthDate': '1955-03-04 00:00:00', 'City': 'London', 'Address': '14 Garrett Hill', 'expanded': 'true',
                               children: [
                                      { 'EmployeeID': 6, 'FirstName': 'Michael', 'LastName': 'Suyama', 'Country': 'UK', 'Title': 'Sales Representative', 'HireDate': '1993-10-17 00:00:00', 'BirthDate': '1963-07-02 00:00:00', 'City': 'London', 'Address': 'Coventry House Miner Rd.' },
                                      { 'EmployeeID': 7, 'FirstName': 'Robert', 'LastName': 'King', 'Country': 'UK', 'Title': 'Sales Representative', 'HireDate': '1994-01-02 00:00:00', 'BirthDate': '1960-05-29 00:00:00', 'City': 'London', 'Address': 'Edgeham Hollow Winchester Way' },
                                      { 'EmployeeID': 9, 'FirstName': 'Anne', 'LastName': 'Dodsworth', 'Country': 'UK', 'Title': 'Sales Representative', 'HireDate': '1994-11-15 00:00:00', 'BirthDate': '1966-01-27 00:00:00', 'City': 'London', 'Address': '7 Houndstooth Rd.' }
                               ]
                           }
                       ]
                   }
        ];
        //// prepare the data
        let source =
        {
            dataType: 'json',
            dataFields: [
                { name: 'EmployeeID', type: 'number' },
                { name: 'FirstName', type: 'string' },
                { name: 'LastName', type: 'string' },
                { name: 'Country', type: 'string' },
                { name: 'City', type: 'string' },
                { name: 'Address', type: 'string' },
                { name: 'Title', type: 'string' },
                { name: 'HireDate', type: 'date' },
                { name: 'children', type: 'array' },
                { name: 'expanded', type: 'bool' },
                { name: 'BirthDate', type: 'date' }
            ],
            hierarchy:
            {
                root: 'children'
            },
            id: 'EmployeeID',
            localData: employees
        };
        let dataAdapter = new $.jqx.dataAdapter(source);
        // create Tree Grid
        let columns = [
            { text: 'FirstName', dataField: 'FirstName', width: 200 },
            { text: 'LastName', dataField: 'LastName', width: 200 },
            { text: 'Title', dataField: 'Title', width: 160 },
            { text: 'Birth Date', dataField: 'BirthDate', cellsFormat: 'd' }
        ];
        return (
            <div>

                <JqxTreeGrid ref='myTreeGrid'
                    source={dataAdapter}
                    width={850}
                    sortable={true}
                    columns={columns}
                />

                <div style={{ fontSize: '13px', fontFamily: 'Verdana', width: '600px', marginTop: '10px' }}>
                    <div style={{ float: 'left', width: '300px' }}>
                        <h4>Column Alignment</h4>
                        <JqxDropDownList ref='columnAlignment' 
                            selectedIndex={0} autoDropDownHeight={true}
                            source={['Left', 'Center', 'Right']} height={25}
                        />
                    </div>
                    <div style={{ float: 'left', width: '300px' }}>
                        <h4>Cells Alignment</h4>
                        <JqxDropDownList ref='cellsAlignment' 
                            selectedIndex={0} autoDropDownHeight={true}
                            source={['Left', 'Center', 'Right']} height={25}
                        />
                    </div>
                </div>

            </div>
        )
    }
}

ReactDOM.render(<App />, document.getElementById('app'));
