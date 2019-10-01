import React, {Fragment} from 'react';
import PropTypes from 'prop-types';
import Row from './row';

export default class Table extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            loading: {
                loaded: false,
                reloading: false,
                error: false,
                errorMessage: '',
            },
            scope: 'global',
            features: [],
        }
    }

    renderScopeSelection() {
        return this.props.scopes.map((item, key) => {
            return (
                <option value={item} key={key}>{item}</option>
            );
        })
    }

    renderRows() {
        if (this.state.loading.reloading || !this.state.loading.loaded) {
            return (
                <tr>
                    <td colSpan="5"><em>{window.Translator.trans('table.column.row.loading', {}, 'feature_flag')}</em></td>
                </tr>
            );
        }

        const rows = [];

        this.state.features.forEach((item, key) => {
            rows.push(
                <Row
                    key={key}
                    featureDefinition={item}
                />
            );
        });

        return rows;
    }

    render() {
        return (
            <Fragment>
                <div className="ez-table-header mt-3">
                    <div className="ez-table-header__headline">{window.Translator.trans('table.header.name', {}, 'feature_flag')}</div>
                    <div>
                        <select className="form-control" value={this.state.scope} disabled={this.state.loading.reloading || !this.state.loading.loaded}>
                            {this.renderScopeSelection()}
                        </select>
                    </div>
                </div>
                <table className="table">
                    <thead>
                        <tr>
                            <th>{window.Translator.trans('table.column.head.name', {}, 'feature_flag')}</th>
                            <th>{window.Translator.trans('table.column.head.description', {}, 'feature_flag')}</th>
                            <th>{window.Translator.trans('table.column.head.state', {}, 'feature_flag')}</th>
                            <th>{window.Translator.trans('table.column.head.source', {}, 'feature_flag')}</th>
                            <th>{window.Translator.trans('table.column.head.change', {}, 'feature_flag')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {this.renderRows()}
                    </tbody>
                </table>
            </Fragment>
        );
    }
}

Table.propTypes = {
    scopes: PropTypes.arrayOf(PropTypes.string).isRequired
};
