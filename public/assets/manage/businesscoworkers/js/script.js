// Business Coworkers D3 Graph Visualization
class BusinessCoworkersGraph {
    constructor(containerId, data) {
        this.containerId = containerId;
        this.data = data;
        this.width = 800;
        this.height = 600;
        this.simulation = null;
        this.svg = null;
        this.tooltip = null;

        this.init();
    }

    init() {
        this.createSVG();
        this.createTooltip();
        this.setupSimulation();
        this.render();
    }

    createSVG() {
        const container = document.getElementById(this.containerId);
        if (!container) return;

        // Clear existing content
        container.innerHTML = '';

        this.svg = d3.select(`#${this.containerId}`)
            .append('svg')
            .attr('width', this.width)
            .attr('height', this.height)
            .attr('viewBox', [0, 0, this.width, this.height])
            .attr('preserveAspectRatio', 'xMidYMid meet');
    }

    createTooltip() {
        this.tooltip = d3.select('body')
            .append('div')
            .attr('class', 'tooltip')
            .style('opacity', 0);
    }

    setupSimulation() {
        this.simulation = d3.forceSimulation(this.data.nodes)
            .force('link', d3.forceLink(this.data.links)
                .id(d => d.id)
                .distance(100)
                .strength(1))
            .force('charge', d3.forceManyBody()
                .strength(-300))
            .force('center', d3.forceCenter(this.width / 2, this.height / 2))
            .force('collision', d3.forceCollide()
                .radius(30));
    }

    render() {
        // Create links
        const link = this.svg.append('g')
            .attr('class', 'links')
            .selectAll('line')
            .data(this.data.links)
            .join('line')
            .attr('class', 'link')
            .attr('stroke-width', d => Math.sqrt(d.value));

        // Create nodes
        const node = this.svg.append('g')
            .attr('class', 'nodes')
            .selectAll('circle')
            .data(this.data.nodes)
            .join('circle')
            .attr('class', 'node')
            .attr('r', 8)
            .attr('fill', d => this.getNodeColor(d.type))
            .call(this.drag(this.simulation))
            .on('mouseover', (event, d) => this.showTooltip(event, d))
            .on('mouseout', () => this.hideTooltip());

        // Add labels to nodes
        const label = this.svg.append('g')
            .attr('class', 'labels')
            .selectAll('text')
            .data(this.data.nodes)
            .join('text')
            .text(d => d.name.length > 10 ? d.name.substring(0, 10) + '...' : d.name)
            .attr('font-size', '10px')
            .attr('dx', 12)
            .attr('dy', 4)
            .attr('fill', '#374151');

        // Update positions on each tick
        this.simulation.on('tick', () => {
            link
                .attr('x1', d => d.source.x)
                .attr('y1', d => d.source.y)
                .attr('x2', d => d.target.x)
                .attr('y2', d => d.target.y);

            node
                .attr('cx', d => d.x)
                .attr('cy', d => d.y);

            label
                .attr('x', d => d.x)
                .attr('y', d => d.y);
        });
    }

    getNodeColor(type) {
        switch (type) {
            case 'source':
                return '#3b82f6'; // blue
            case 'destination':
                return '#ef4444'; // red
            default:
                return '#6b7280'; // gray
        }
    }

    showTooltip(event, d) {
        this.tooltip
            .transition()
            .duration(200)
            .style('opacity', .9);
        this.tooltip
            .html(`
                <div class="font-semibold">${d.name}</div>
                <div class="text-sm">شناسه: ${d.id}</div>
                <div class="text-sm">نوع: ${d.type}</div>
            `)
            .style('left', (event.pageX + 10) + 'px')
            .style('top', (event.pageY - 28) + 'px');
    }

    hideTooltip() {
        this.tooltip
            .transition()
            .duration(500)
            .style('opacity', 0);
    }

    drag(simulation) {
        function dragstarted(event) {
            if (!event.active) simulation.alphaTarget(0.3).restart();
            event.subject.fx = event.subject.x;
            event.subject.fy = event.subject.y;
        }

        function dragged(event) {
            event.subject.fx = event.x;
            event.subject.fy = event.y;
        }

        function dragended(event) {
            if (!event.active) simulation.alphaTarget(0);
            event.subject.fx = null;
            event.subject.fy = null;
        }

        return d3.drag()
            .on('start', dragstarted)
            .on('drag', dragged)
            .on('end', dragended);
    }

    updateData(newData) {
        this.data = newData;
        this.setupSimulation();
        this.render();
    }

    resize(width, height) {
        this.width = width;
        this.height = height;
        this.svg.attr('viewBox', [0, 0, width, height]);
        this.simulation.force('center', d3.forceCenter(width / 2, height / 2));
    }
}

// Initialize graph when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have graph data available
    if (typeof window.graphData !== 'undefined') {
        const graphContainer = document.getElementById('graph-container');
        if (graphContainer) {
            const graph = new BusinessCoworkersGraph('graph-container', window.graphData);

            // Make graph available globally for debugging
            window.businessCoworkersGraph = graph;
        }
    }
});

// Handle tab switching for graph rendering
document.addEventListener('alpine:init', () => {
    Alpine.data('businessCoworkers', () => ({
        activeTab: 'list',

        init() {
            // Watch for tab changes to properly render graph
            this.$watch('activeTab', (value) => {
                if (value === 'graph') {
                    // Delay graph rendering to ensure DOM is ready
                    setTimeout(() => {
                        this.renderGraph();
                    }, 100);
                }
            });
        },

        renderGraph() {
            if (typeof window.graphData !== 'undefined' &&
                typeof window.businessCoworkersGraph !== 'undefined') {
                // Update graph with new dimensions
                const container = document.getElementById('graph-container');
                if (container) {
                    const rect = container.getBoundingClientRect();
                    window.businessCoworkersGraph.resize(rect.width, rect.height);
                }
            }
        }
    }));
});
