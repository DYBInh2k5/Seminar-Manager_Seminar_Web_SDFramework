import React, { useMemo, useState } from 'react';

const STATUS_COLORS = {
    pending: 'var(--primary)',
    approved: 'var(--accent)',
    rejected: 'var(--danger)',
};

const ROLE_COLORS = {
    admin: '#5b4b8a',
    lecturer: 'var(--accent)',
    student: '#c67b1f',
};

function SummaryRow({ label, value, percent, color }) {
    return (
        <div className="analytics-row">
            <div className="analytics-row-head">
                <strong>{label}</strong>
                <span className="muted small">
                    {value} {percent !== null ? `(${percent}%)` : ''}
                </span>
            </div>
            <div className="analytics-meter">
                <span style={{ width: `${Math.max(percent ?? 0, value > 0 ? 8 : 0)}%`, background: color }} />
            </div>
        </div>
    );
}

function SegmentCard({ title, eyebrow, description, items, colorMap, metricMode }) {
    const total = useMemo(() => items.reduce((sum, item) => sum + item.value, 0), [items]);

    return (
        <section className="card analytics-card">
            <div className="section-head">
                <div>
                    <span className="eyebrow">{eyebrow}</span>
                    <h2>{title}</h2>
                </div>
                <span className="badge">{total}</span>
            </div>

            <p className="muted analytics-description">{description}</p>

            <div className="stack-list">
                {items.map((item) => {
                    const percent = total > 0 ? Math.round((item.value / total) * 100) : 0;

                    return (
                        <SummaryRow
                            key={item.key}
                            label={item.label}
                            value={item.value}
                            percent={metricMode === 'percent' ? percent : null}
                            color={colorMap[item.key] ?? 'var(--accent)'}
                        />
                    );
                })}
            </div>
        </section>
    );
}

function Leaderboard({ lecturers }) {
    return (
        <section className="card spaced-card">
            <div className="section-head">
                <div>
                    <span className="eyebrow">Leaderboard</span>
                    <h2>Top lecturers by approved registrations</h2>
                </div>
            </div>

            <div className="stack-list">
                {lecturers.length > 0 ? (
                    lecturers.map((lecturer, index) => (
                        <div key={`${lecturer.name}-${index}`} className="list-item analytics-list-item">
                            <div>
                                <strong>{lecturer.name}</strong>
                                <div className="muted small">Topics: {lecturer.topicsCount}</div>
                            </div>
                            <span className="badge approved">{lecturer.approvedRegistrationsCount} approved</span>
                        </div>
                    ))
                ) : (
                    <p className="muted">No lecturer analytics available yet.</p>
                )}
            </div>
        </section>
    );
}

function buildPalette(items, fallback) {
    return items.reduce((map, item, index) => {
        map[item.key] = fallback[index % fallback.length];
        return map;
    }, {});
}

export default function DashboardAnalytics({ statusBreakdown, roleBreakdown, departmentBreakdown = [], categoryBreakdown = [], topLecturers, showLeaderboard }) {
    const [metricMode, setMetricMode] = useState('count');

    const statusItems = [
        { key: 'pending', label: 'Pending', value: statusBreakdown.pending ?? 0 },
        { key: 'approved', label: 'Approved', value: statusBreakdown.approved ?? 0 },
        { key: 'rejected', label: 'Rejected', value: statusBreakdown.rejected ?? 0 },
    ];

    const roleItems = [
        { key: 'admin', label: 'Admin', value: roleBreakdown.admin ?? 0 },
        { key: 'lecturer', label: 'Lecturer', value: roleBreakdown.lecturer ?? 0 },
        { key: 'student', label: 'Student', value: roleBreakdown.student ?? 0 },
    ];

    const departmentItems = departmentBreakdown.map((item) => ({
        key: item.label,
        label: item.label,
        value: item.value,
    }));

    const categoryItems = categoryBreakdown.map((item) => ({
        key: item.label,
        label: item.label,
        value: item.value,
    }));

    const departmentColors = buildPalette(departmentItems, ['#1b4332', '#6f4e37', '#386641', '#bc6c25', '#5b4b8a']);
    const categoryColors = buildPalette(categoryItems, ['#264653', '#2a9d8f', '#e9c46a', '#f4a261', '#8d99ae']);

    return (
        <>
            <div className="analytics-toolbar">
                <div>
                    <span className="eyebrow">React module</span>
                    <h2 className="analytics-title">Interactive analytics</h2>
                    <p className="muted analytics-description">
                        Dashboard charts are rendered with React while the rest of the application stays on Laravel Blade.
                    </p>
                </div>

                <div className="segmented-control" role="tablist" aria-label="Analytics display mode">
                    <button
                        type="button"
                        className={`segmented-button ${metricMode === 'count' ? 'active' : ''}`}
                        onClick={() => setMetricMode('count')}
                    >
                        Count view
                    </button>
                    <button
                        type="button"
                        className={`segmented-button ${metricMode === 'percent' ? 'active' : ''}`}
                        onClick={() => setMetricMode('percent')}
                    >
                        Percent view
                    </button>
                </div>
            </div>

            <div className="grid two">
                <SegmentCard
                    title="Registration status breakdown"
                    eyebrow="Analytics"
                    description="This panel shows the current registration pipeline and makes it easier to compare approval outcomes."
                    items={statusItems}
                    colorMap={STATUS_COLORS}
                    metricMode={metricMode}
                />

                <SegmentCard
                    title="User roles"
                    eyebrow="People"
                    description="This panel summarizes how the system is distributed across administrative, teaching, and student accounts."
                    items={roleItems}
                    colorMap={ROLE_COLORS}
                    metricMode={metricMode}
                />
            </div>

            <div className="grid two">
                <SegmentCard
                    title="Departments represented"
                    eyebrow="Academic data"
                    description="A quick snapshot of which departments and programs are currently represented in the portal."
                    items={departmentItems}
                    colorMap={departmentColors}
                    metricMode={metricMode}
                />

                <SegmentCard
                    title="Topic categories"
                    eyebrow="Portfolio"
                    description="This panel highlights how the seminar archive is distributed across topic categories."
                    items={categoryItems}
                    colorMap={categoryColors}
                    metricMode={metricMode}
                />
            </div>

            {showLeaderboard ? <Leaderboard lecturers={topLecturers} /> : null}
        </>
    );
}
